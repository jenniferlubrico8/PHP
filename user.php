<?php
session_start();

// Security: Prevent unauthenticated users
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

require 'db.php';
$user_id = $_SESSION['user_id'];
$success_msg = "";

// Handle Task Addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['task_title']);
    $desc = trim($_POST['task_desc']);
    
    if ($title !== "") {
        $stmt = $pdo->prepare("INSERT INTO Tasks (user_id, title, description) VALUES (:uid, :title, :desc)");
        $stmt->execute(['uid' => $user_id, 'title' => $title, 'desc' => $desc]);
        $success_msg = "Task added successfully.";
    }
}

// Handle Task Deletion
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM Tasks WHERE id = :id AND user_id = :uid");
    $stmt->execute(['id' => $task_id, 'uid' => $user_id]);
    header("Location: user.php?deleted=1");
    exit();
}

if (isset($_GET['deleted'])) {
    $success_msg = "Task removed.";
}

// Fetch all active tasks for this user
$stmt = $pdo->prepare("SELECT * FROM Tasks WHERE user_id = :uid ORDER BY id DESC");
$stmt->execute(['uid' => $user_id]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Tasks Workspace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #10141B; --panel: #171C25; --card: #1A2029; --line: #2A313D; --text: #E7E9EC; --text-dim: #8B939E; --accent: #E8B563; --error: #E8697A; --ok: #6FCF97; }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  
  .frame { width: 100%; max-width: 920px; display: grid; grid-template-columns: 280px 1fr; background: var(--panel); border: 1px solid var(--line); min-height: 560px; align-items: stretch; }
  @media (max-width: 760px) { .frame { grid-template-columns: 1fr; } .status-side { display: none; } }
  
  .status-side { padding: 40px 36px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: space-between; background-image: linear-gradient(var(--line) 1px, transparent 1px), linear-gradient(90deg, var(--line) 1px, transparent 1px); background-size: 28px 28px; background-position: -1px -1px; }
  .status-header { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); }
  .status-header .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: var(--ok); margin-right: 8px; box-shadow: 0 0 8px var(--ok); }
  .status-list { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); line-height: 2.1; }
  .status-list .row { display: flex; justify-content: space-between; border-bottom: 1px dashed var(--line); padding: 4px 0; }
  .status-list .val { color: var(--text); }
  .status-quote { font-family: 'Space Grotesk', sans-serif; font-size: 22px; line-height: 1.3; color: var(--text); max-width: 280px; }
  .status-quote span { color: var(--accent); }
  
  .form-side { padding: 48px 44px; display: flex; flex-direction: column; background: var(--card); }
  .form-side h2 { font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 600; margin: 0 0 6px; }
  .form-side .sub { color: var(--text-dim); font-size: 14px; margin: 0 0 28px; }
  
  .success { background: rgba(111, 207, 151, 0.1); border: 1px solid rgba(111, 207, 151, 0.4); color: var(--ok); font-size: 13px; padding: 10px 12px; border-radius: 3px; margin-bottom: 24px; }
  
  form { display: flex; flex-direction: column; gap: 14px; margin-bottom: 32px;}
  label { font-size: 13px; color: var(--text-dim); display: block; margin-bottom: 6px; }
  input, textarea { width: 100%; background: var(--bg); border: 1px solid var(--line); color: var(--text); font-family: 'Inter', sans-serif; font-size: 14px; padding: 11px 12px; border-radius: 3px; outline: none; transition: 0.15s; }
  textarea { resize: vertical; min-height: 80px; }
  input:focus, textarea:focus { border-color: var(--accent); }
  button { width: max-content; background: var(--accent); color: #1A1406; border: none; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600; padding: 10px 18px; border-radius: 3px; cursor: pointer; transition: 0.15s; }
  button:hover { filter: brightness(1.08); }
  
  .section-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 10px; }
  .section-head h3 { font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 600; margin: 0; }
  .section-head .count { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); }
  
  table { width: 100%; border-collapse: collapse; background: var(--bg); border: 1px solid var(--line); border-radius: 3px; overflow: hidden; margin-bottom: auto;}
  th, td { text-align: left; padding: 11px 14px; border-bottom: 1px solid var(--line); font-size: 13px; vertical-align: top;}
  th { font-family: 'JetBrains Mono', monospace; font-weight: 400; font-size: 11px; color: var(--text-dim); }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: rgba(232, 181, 99, 0.04); }
  
  .task-title { display: block; font-weight: 500; font-size: 14px; color: var(--text); margin-bottom: 4px; }
  .task-desc { color: var(--text-dim); font-size: 13px; line-height: 1.4; display: block; }
  .empty { padding: 24px 14px; color: var(--text-dim); text-align: center; font-style: italic; font-size: 13px; }
  
  .action-btn { color: var(--error); font-family: 'JetBrains Mono', monospace; font-size: 12px; text-decoration: none; }
  .action-btn:hover { text-decoration: underline; }
  
  .foot { margin-top: 32px; font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-dim); border-top: 1px solid var(--line); padding-top: 16px; }
  .foot a { color: var(--accent); text-decoration: none; }
  .foot a:hover { text-decoration: underline; }
</style>
</head>
<body>
  <div class="frame">
    <div class="status-side">
      <div>
        <div class="status-header"><span class="dot"></span>WORKSPACE ACTIVE</div>
        <div class="status-list">
          <div class="row"><span>uptime</span><span class="val">held for a while</span></div>
          <div class="row"><span>session</span><span class="val">active</span></div>
          <div class="row"><span>tasks</span><span class="val"><?php echo count($tasks); ?> active</span></div>
        </div>
      </div>
      <div class="status-quote">Manage your <span>daily tasks</span> in your personal workspace.</div>
    </div>
    
    <div class="form-side">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
      <p class="sub">You're signed in to your dashboard.</p>
      
      <?php if ($success_msg): ?><div class="success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>

      <!-- Task Form -->
      <form method="POST" autocomplete="off">
        <div>
          <label>Task Title</label>
          <input type="text" name="task_title" required>
        </div>
        <div>
          <label>Description (Optional)</label>
          <textarea name="task_desc"></textarea>
        </div>
        <button type="submit" name="add_task">Add Task</button>
      </form>

      <!-- Task Workspace Table -->
      <div class="section-head">
        <h3>My Active Workspace</h3>
        <span class="count"><?php echo count($tasks); ?> tasks</span>
      </div>
      
      <table>
        <thead>
          <tr>
            <th style="width: 70px; text-align: center;">Status</th>
            <th>Task Details</th>
            <th style="width: 100px; text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($tasks)): ?>
            <tr><td colspan="3" class="empty">You haven't added any tasks yet!</td></tr>
          <?php else: ?>
            <?php foreach ($tasks as $t): ?>
              <tr>
                <td style="text-align: center; padding-top: 14px;">
                    <input type="checkbox" checked disabled style="width: 14px; height: 14px; accent-color: var(--ok);">
                </td>
                <td>
                    <span class="task-title"><?php echo htmlspecialchars($t['title']); ?></span>
                    <span class="task-desc"><?php echo nl2br(htmlspecialchars($t['description'])); ?></span>
                </td>
                <td style="text-align: right; padding-top: 14px;">
                    <a href="?delete=<?php echo $t['id']; ?>" class="action-btn" onclick="return confirm('Remove this task?');">[X] Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="foot">signed in as <?php echo htmlspecialchars($_SESSION['username']); ?> &middot; <a href="logout.php">log out</a></div>
    </div>
  </div>
</body>
</html>
