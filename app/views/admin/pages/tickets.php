<section class="page">
<style>
* {
    color: white !important;
}
</style>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h1>Support Tickets</h1>
        <button class="btn btn-primary"><i class="fas fa-user-headset"></i> Assign to Staff</button>
    </div>

    <div class="glass" style="margin-bottom:16px;">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:12px;">
            <select class="form-control"><option>Status</option><option>Open</option><option>Closed</option><option>Waiting</option></select>
            <select class="form-control"><option>Priority</option><option>Low</option><option>Normal</option><option>High</option></select>
            <input class="form-control" type="text" placeholder="Search subject or user" />
        </div>
    </div>

    <div class="glass">
        <table class="table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>User</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#94a3b8;">No support tickets found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?php echo e($ticket['subject']); ?></td>
                            <td><?php echo e($ticket['username']); ?> <br><small style="color:#94a3b8;"><?php echo e($ticket['email']); ?></small></td>
                            <td><span class="badge <?php echo $ticket['priority'] === 'high' ? 'badge-danger' : ($ticket['priority'] === 'normal' ? 'badge-warning' : 'badge-info'); ?>"><?php echo ucfirst($ticket['priority']); ?></span></td>
                            <td><span class="badge <?php echo $ticket['status'] === 'open' ? 'badge-success' : 'badge-warning'; ?>"><?php echo ucfirst($ticket['status']); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($ticket['created_at'])); ?></td>
                            <td><a href="#" class="btn btn-outline" style="padding:6px 10px;">Reply</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
