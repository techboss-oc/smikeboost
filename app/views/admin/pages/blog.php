<div class="page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Blog Posts</h1>
        <button onclick="document.getElementById('newPostModal').style.display='block'" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Post
        </button>
    </div>

    <div class="glass">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                    <td>
                        <a href="<?php echo url('blog/' . $post['slug']); ?>" target="_blank" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- New Post Modal -->
<div id="newPostModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; overflow-y:auto;">
    <div style="max-width:800px; margin:50px auto; background:#1a1b23; padding:30px; border-radius:16px; border:1px solid rgba(255,255,255,0.1);">
        <h2 style="margin-bottom:20px;">Create New Post</h2>
        <form method="POST" action="<?php echo admin_url('blog'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo admin_csrf_token(); ?>">
            <input type="hidden" name="action" value="create">
            
            <div style="margin-bottom:15px;">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            
            <div style="margin-bottom:15px;">
                <label>Featured Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            
            <div style="margin-bottom:15px;">
                <label>Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="3"></textarea>
            </div>
            
            <div style="margin-bottom:15px;">
                <label>Content (HTML)</label>
                <textarea name="content" class="form-control" rows="10" required></textarea>
            </div>
            
            <div style="margin-bottom:15px;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('newPostModal').style.display='none'" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Post</button>
            </div>
        </form>
    </div>
</div>
