<?php
/**
 * API Documentation Page
 */
$seo = get_seo_tags(
  'API Documentation',
  'Integrate SmikeBoost programmatically through a simple RESTful API.',
  'SmikeBoost API, SMM API, social panel API'
);

$v2Endpoint = rtrim(API_BASE_URL, '/') . '/v2';
$restServicesEndpoint = rtrim(API_BASE_URL, '/') . '/services';

$sampleCurl = <<<CURL
curl -X POST \\
  -H "Content-Type: application/x-www-form-urlencoded" \\
  -d "key=YOUR_API_KEY" \\
  -d "action=services" \\
  {$v2Endpoint}
CURL;

$samplePhp = <<<PHP
<?php
$payload = http_build_query([
  'key' => 'YOUR_API_KEY',
  'action' => 'add',
  'service' => 101,
  'link' => 'https://instagram.com/example',
  'quantity' => 1000,
]);

$ch = curl_init('{$v2Endpoint}');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $payload,
]);
$response = curl_exec($ch);
curl_close($ch);

var_dump(json_decode($response, true));
PHP;

$sampleNode = <<<NODE
import fetch from 'node-fetch';

const form = new URLSearchParams();
form.append('key', 'YOUR_API_KEY');
form.append('action', 'balance');

const response = await fetch('{$v2Endpoint}', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: form
});

console.log(await response.json());
NODE;

$actions = [
  ['action' => 'services', 'description' => 'List every active service with price, min/max, and category.'],
  ['action' => 'add', 'description' => 'Create a new order (service, link, quantity required).'],
  ['action' => 'status', 'description' => 'Check a single order (order) or comma separated list (orders=1,2,3).'],
  ['action' => 'balance', 'description' => 'Return the wallet balance and currency.'],
];
?>

<section class="api-page section" style="padding-top: 1rem;">
    <div class="container">
        <div class="page-header" style="padding-bottom: 1rem;">
            <p class="eyebrow">Developers</p>
            <h1 class="text-gradient">SmikeBoost API</h1>
            <p>Automate orders, sync prices to your own reseller panel, or reconcile balances programmatically.</p>
        </div>

        <div class="grid grid-1" style="gap: 3rem;">
            <!-- Base URL Card -->
            <div class="glass-card">
                <div class="api-endpoint-card">
                    <h3><i class="fas fa-link text-primary"></i> API Endpoint</h3>
                    <span class="badge">v2</span>
                </div>
                <div class="api-code-box">
                    <code><?php echo htmlspecialchars($v2Endpoint, ENT_QUOTES, 'UTF-8'); ?></code>
                    <button class="btn btn-sm btn-outline" onclick="copyToClipboard('<?php echo $v2Endpoint; ?>', this)">
                        <i class="far fa-copy"></i> Copy
                    </button>
                </div>
                <p class="note" style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Send <code>key</code> (API key) and <code>action</code> with every POST request.
                </p>
            </div>

            <!-- Actions Grid -->
            <div class="grid grid-2">
                <?php foreach ($actions as $item): ?>
                <div class="glass-card" style="transition: transform 0.3s ease;">
                    <div class="action-header" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <span class="badge" style="background: var(--color-primary); color: white;">POST</span>
                        <code style="font-weight: bold; color: var(--text-main);">action=<?php echo htmlspecialchars($item['action'], ENT_QUOTES, 'UTF-8'); ?></code>
                    </div>
                    <p style="color: var(--text-muted); font-size: 0.95rem;"><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Code Examples -->
            <div class="glass-card">
                <h3 style="margin-bottom: 1.5rem;">Code Examples</h3>
                <div class="tabs">
                    <div class="tab-header">
                        <button class="tab-btn active" data-tab="curl">cURL</button>
                        <button class="tab-btn" data-tab="php">PHP</button>
                        <button class="tab-btn" data-tab="node">Node.js</button>
                    </div>
                    <div class="tab-content">
                        <div id="curl" class="tab-pane active">
                            <pre><code class="language-bash"><?php echo htmlspecialchars($sampleCurl, ENT_QUOTES, 'UTF-8'); ?></code></pre>
                        </div>
                        <div id="php" class="tab-pane">
                            <pre><code class="language-php"><?php echo htmlspecialchars($samplePhp, ENT_QUOTES, 'UTF-8'); ?></code></pre>
                        </div>
                        <div id="node" class="tab-pane">
                            <pre><code class="language-javascript"><?php echo htmlspecialchars($sampleNode, ENT_QUOTES, 'UTF-8'); ?></code></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Responses & Errors -->
            <div class="glass-card">
                <h3 style="margin-bottom: 1.5rem;">Responses & Errors</h3>
                <ul style="list-style: none; padding: 0; display: grid; gap: 1rem;">
                    <li style="display: flex; gap: 1rem; align-items: flex-start;">
                        <span class="badge" style="background: var(--color-success); color: white; min-width: 60px; text-align: center;">200</span>
                        <span style="color: var(--text-muted);">Successful request, body contains JSON data.</span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start;">
                        <span class="badge" style="background: var(--color-warning); color: white; min-width: 60px; text-align: center;">400</span>
                        <span style="color: var(--text-muted);">Missing parameters or unsupported action.</span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start;">
                        <span class="badge" style="background: var(--color-danger); color: white; min-width: 60px; text-align: center;">401</span>
                        <span style="color: var(--text-muted);">API key missing or invalid.</span>
                    </li>
                    <li style="display: flex; gap: 1rem; align-items: flex-start;">
                        <span class="badge" style="background: var(--text-dim); color: white; min-width: 60px; text-align: center;">404</span>
                        <span style="color: var(--text-muted);">Endpoint or order not linked to your account.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active class to clicked
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });
});

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied';
        btn.classList.add('btn-primary');
        btn.classList.remove('btn-outline');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
        }, 2000);
    });
}
</script>
