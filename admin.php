<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You must be an administrator.");
}

require 'db.php';
$stmt = $pdo->query("SELECT username, role FROM Users ORDER BY username ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Area</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #10141B; --panel: #171C25; --card: #1A2029; --line: #2A313D; --text: #E7E9EC; --text-dim: #8B939E; --accent: #E8B563; --accent-dim: #6B5A3A; --error: #E8697A; --ok: #6FCF97; }
  * { box-sizing: border-box; }
  body { margin: 0; padding: 0; background: var(--bg); color: var(--text); font-family: 'Inter', system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .frame { width: 100%; max-width: 920px; display: grid; grid-template-columns: 280px 1fr; background: var(--panel); border: 1px solid var(--line); min-height: 560px; }
  @media (max-width: 760px) { .frame { grid-template-columns: 1fr; min-height: auto; } .status-side { display: none; } }
  
  .status-side { padding: 40px 36px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: space-between; background-image: linear-gradient(var(--line) 1px, transparent 1px), linear-gradient(90deg, var(--line) 1px, transparent 1px); background-size: 28px 28px; background-position: -1px -1px; background-color: var(--panel); }
  .status-header { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); letter-spacing: 0.02em; }
  .status-header .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: var(--ok); margin-right: 8px; box-shadow: 0 0 8px var(--ok); }
  .status-list { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); line-height: 2.1; }
  .status-list .row { display: flex; justify-content: space-between; border-bottom: 1px dashed var(--line); padding: 4px 0; }
  .status-list .val { color: var(--text); }
  .status-quote { font-family: 'Space Grotesk', sans-serif; font-size: 22px; line-height: 1.3; color: var(--text); max-width: 280px; }
  .status-quote span { color: var(--accent); }
  
  .form-side { padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; background: var(--card); }
  .form-side h2 { font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 600; margin: 0 0 6px; }
  .form-side .sub { color: var(--text-dim); font-size: 14px; margin: 0 0 28px; }
  .section-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 10px; }
  .section-head h3 { font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 600; margin: 0; }
  .section-head .count { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); }
  
  table { width: 100%; border-collapse: collapse; background: var(--bg); border: 1px solid var(--line); border-radius: 3px; overflow: hidden; margin-bottom: auto;}
  th, td { text-align: left; padding: 11px 14px; border-bottom: 1px solid var(--line); font-size: 13px; }
  th { font-family: 'JetBrains Mono', monospace; font-weight: 400; font-size: 11px; color: var(--text-dim); }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: rgba(232, 181, 99, 0.04); }
  th.role, td.role { text-align: right; }
  
  .badge { display: inline-block; font-family: 'JetBrains Mono', monospace; font-size: 11px; padding: 3px 10px; border-radius: 999px; border: 1px solid var(--line); color: var(--text-dim); }
  .badge.admin { border-color: var(--accent-dim); color: var(--accent); }
  .empty { padding: 20px 14px; color: var(--text-dim); font-size: 13px; }
  
  .foot { margin-top: 24px; font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-dim); border-top: 1px solid var(--line); padding-top: 16px;}
  .foot a { color: var(--accent); text-decoration: none; }
  .foot a:hover { text-decoration: underline; }
</style>
</head>
<body>
  <div class="frame">
    <div class="status-side">
      <div>
        <div class="status-header"><span class="dot"></span>ALL SYSTEMS OPERATIONAL</div>
        <div class="status-list">
          <div class="row"><span>uptime</span><span class="val">held for a while</span></div>
          <div class="row"><span>session</span><span class="val">active</span></div>
          <div class="row"><span>role</span><span class="val"><?php echo htmlspecialchars($_SESSION['role']); ?></span></div>
        </div>
      </div>
      <div class="status-quote">Access is <span>role-based</span>. You're viewing the admin area.</div>
    </div>
    <div class="form-side">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
      <p class="sub">You're signed in with administrator access.</p>
      
      <div class="section-head">
        <h3>Registered accounts</h3>
        <span class="count"><?php echo htmlspecialchars(count($users)); ?> total</span>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th class="role">Role</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="2" class="empty">No accounts have registered yet.</td></tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td class="role">
                  <span class="badge<?php echo $u['role'] === 'admin' ? ' admin' : ''; ?>">
                    <?php echo htmlspecialchars($u['role']); ?>
                  </span>
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
