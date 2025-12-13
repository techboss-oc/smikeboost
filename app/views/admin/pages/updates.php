<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="margin:0; color:#fff;">Updates (Ticker Tape)</h1>
    </div>

    <div class="form-grid">
        <div class="glass">
            <h3 class="section-title"><i class="fas fa-plus"></i> Create Update</h3>
            <form action="<?php echo admin_url('updates'); ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                <input type="hidden" name="action" value="create">

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="2" required placeholder="Short update message shown in the ticker..."></textarea>
                </div>

                <div class="form-group" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="is_active" id="create-active" checked>
                    <label for="create-active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Publish Update</button>
            </form>
        </div>

        <div class="glass">
            <h3 class="section-title"><i class="fas fa-history"></i> Recent Updates</h3>
            <?php if (empty($updates)): ?>
                <p style="color:#9ca3af; text-align:center; padding:20px;">No updates yet.</p>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php foreach ($updates as $u): ?>
                        <div style="background:rgba(255,255,255,0.03); border-left:3px solid #60a5fa; padding:12px; border-radius:4px; position:relative;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; gap:8px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <i class="fas fa-bullhorn text-primary"></i>
                                    <strong style="color:#fff;">Ticker Update</strong>
                                    <span style="font-size:0.75rem; padding:2px 8px; border-radius:999px; background:rgba(255,255,255,0.06); color:<?php echo $u['is_active'] ? '#34d399' : '#f87171'; ?>;">
                                        <?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <button class="btn btn-outline" style="padding:6px 10px;" type="button" data-edit-toggle="<?php echo $u['id']; ?>"><i class="fas fa-pen"></i> Edit</button>
                                    <form action="<?php echo admin_url('updates'); ?>" method="post" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button class="btn btn-outline" style="padding:6px 10px;" type="submit"><i class="fas fa-toggle-on"></i> Toggle</button>
                                    </form>
                                    <form action="<?php echo admin_url('updates'); ?>" method="post" onsubmit="return confirm('Delete this update?');" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button class="btn btn-outline" style="padding:6px 10px;" type="submit"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div style="color:#d1d5db; font-size:0.9rem; margin-bottom:6px;"><?php echo htmlspecialchars($u['message']); ?></div>
                            <div style="color:#6b7280; font-size:0.75rem; margin-bottom:8px;">
                                Created: <?php echo date('M j, H:i', strtotime($u['created_at'])); ?>
                            </div>
                            <div id="edit-form-<?php echo $u['id']; ?>" style="display:none; padding-top:8px; border-top:1px solid rgba(255,255,255,0.08); margin-top:8px;">
                                <form action="<?php echo admin_url('updates'); ?>" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    <div class="form-group" style="margin-bottom:12px;">
                                        <label>Message</label>
                                        <textarea name="message" class="form-control" rows="2" required><?php echo htmlspecialchars($u['message']); ?></textarea>
                                    </div>
                                    <div class="form-group" style="margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                                        <input type="checkbox" name="is_active" id="active-<?php echo $u['id']; ?>" <?php echo $u['is_active'] ? 'checked' : ''; ?>>
                                        <label for="active-<?php echo $u['id']; ?>">Active</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-edit-toggle]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-edit-toggle');
                var el = document.getElementById('edit-form-' + id);
                if (!el) return;
                el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
            });
        });
    </script>
</section>