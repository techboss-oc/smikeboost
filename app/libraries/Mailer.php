<?php

class Mailer
{
    public function send($to, $subject, $template, $data = [])
    {
        $content = $this->render($template, $data);
        $layout = $this->render('layout', ['content' => $content, 'subject' => $subject]);
        $fromEmail = get_setting('smtp_from_email', MAIL_FROM);
        $fromName = get_setting('smtp_from_name', MAIL_FROM_NAME);
        $host = get_setting('smtp_host');
        $user = get_setting('smtp_username');
        $pass = get_setting('smtp_password');
        $port = (int) get_setting('smtp_port', 587);
        $secureEnabled = get_setting('smtp_secure') ? true : false;
        if ($host && $user && $pass) {
            $ok = $this->smtpSend($to, $subject, $layout, $fromEmail, $fromName, $host, $port, $user, $pass, $secureEnabled);
            if ($ok) return true;
        }
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";
        $fallback = mail($to, $subject, $layout, $headers);
        $this->log('mail() fallback ' . ($fallback ? 'succeeded' : 'failed'));
        return $fallback;
    }

    private function render($template, $data)
    {
        // If rendering the email layout, always use file-based layout
        if ($template === 'layout') {
            extract($data);
            ob_start();
            $templatePath = APP_PATH . '/views/emails/layout.php';
            if (file_exists($templatePath)) {
                include $templatePath;
            }
            return ob_get_clean();
        }

        // Prefer admin-defined auto email templates when available
        $row = null;
        try {
            $row = db_fetch("SELECT subject, body, is_active FROM auto_emails WHERE trigger_event = :ev LIMIT 1", ['ev' => $template]);
        } catch (Throwable $e) {
        }

        if ($row && (int)($row['is_active'] ?? 0) === 1) {
            $body = $this->interpolate($row['body'] ?? '', $data);
            return $body;
        }

        // Fallback to file-based template
        extract($data);
        ob_start();
        $templatePath = APP_PATH . '/views/emails/' . $template . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }

    private function smtpSend($to, $subject, $body, $fromEmail, $fromName, $host, $port, $user, $pass, $secureEnabled)
    {
        $useSslWrapper = $secureEnabled && (int)$port === 465;
        $address = ($useSslWrapper ? ('ssl://' . $host) : $host);
        $fp = @fsockopen($address, (int)$port, $errno, $errstr, 20);
        if (!$fp) {
            $this->log('SMTP connect failed: ' . $errstr . ' (' . $errno . ')');
            return false;
        }
        $read = function () use ($fp) {
            return fgets($fp, 1024);
        };
        $write = function ($line) use ($fp) {
            fwrite($fp, $line . "\r\n");
        };

        $banner = $read();
        $this->log('SMTP banner: ' . trim((string)$banner));

        $write('EHLO smikeboost.local');
        $ehlo = $read();
        $this->log('EHLO: ' . trim((string)$ehlo));

        // STARTTLS for TLS on ports like 587
        if ($secureEnabled && !$useSslWrapper) {
            $write('STARTTLS');
            $resp = $read();
            $this->log('STARTTLS: ' . trim((string)$resp));
            if (strpos($resp ?? '', '220') !== 0) {
                fclose($fp);
                return false;
            }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->log('TLS negotiation failed');
                fclose($fp);
                return false;
            }
            $write('EHLO smikeboost.local');
            $ehlo2 = $read();
            $this->log('EHLO (post TLS): ' . trim((string)$ehlo2));
        }

        $write('AUTH LOGIN');
        $auth1 = $read();
        $write(base64_encode($user));
        $auth2 = $read();
        $write(base64_encode($pass));
        $auth3 = $read();
        $this->log('AUTH sequence: ' . trim((string)$auth1) . ' | ' . trim((string)$auth2) . ' | ' . trim((string)$auth3));

        $write('MAIL FROM:<' . $fromEmail . '>');
        $mf = $read();
        $write('RCPT TO:<' . $to . '>');
        $rcpt = $read();
        $write('DATA');
        $dataResp = $read();

        $headers = '';
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";
        $headers .= 'To: <' . $to . '>' . "\r\n";
        $headers .= 'Subject: ' . $subject . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $lines = $headers . "\r\n" . $body . "\r\n" . '.';
        $write($lines);
        $final = $read();
        $write('QUIT');
        fclose($fp);

        $this->log('MAIL FROM: ' . trim((string)$mf) . ' | RCPT: ' . trim((string)$rcpt) . ' | DATA: ' . trim((string)$dataResp) . ' | FINAL: ' . trim((string)$final));

        // Basic success heuristic
        return (strpos($final ?? '', '250') === 0) || (strpos($final ?? '', '2') === 0);
    }

    private function interpolate($text, $data)
    {
        $replacements = [];
        foreach ($data as $k => $v) {
            if (is_scalar($v)) {
                $replacements['{' . $k . '}'] = (string)$v;
            }
        }
        return strtr((string)$text, $replacements);
    }

    private function log($message)
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents(APP_PATH . '/email_debug.log', $line, FILE_APPEND);
    }
}
