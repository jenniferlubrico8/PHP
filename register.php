<?php
session_start();
require 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    
    // Default to 'user', but allow 'admin' if selected
    $role = (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'admin' : 'user';

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM Users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        if ($stmt->fetch()) {
            $error = "That username is already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO Users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute([
                'username' => $username,
                'password' => $hash,
                'role'     => $role
            ]);
            $success = "Account created! You can now log in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #10141B; --panel: #171C25; --card: #1A2029; --line: #2A313D; --text: #E7E9EC; --text-dim: #8B939E; --accent: #E8B563; --error: #E8697A; --ok: #6FCF97; }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  
  .frame { width: 100%; max-width: 920px; display: grid; grid-template-columns: 1fr 1fr; background: var(--panel); border: 1px solid var(--line); min-height: 560px; }
  @media (max-width: 760px) { .frame { grid-template-columns: 1fr; min-height: auto; } .status-side { display: none; } }
  
  .status-side { padding: 40px 36px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: space-between; background-image: linear-gradient(var(--line) 1px, transparent 1px), linear-gradient(90deg, var(--line) 1px, transparent 1px); background-size: 28px 28px; background-position: -1px -1px; }
  .status-header { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); }
  .status-header .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: var(--ok); margin-right: 8px; box-shadow: 0 0 8px var(--ok); }
  .status-list { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); line-height: 2.1; }
  .status-list .row { display: flex; justify-content: space-between; border-bottom: 1px dashed var(--line); padding: 4px 0; }
  .status-list .val { color: var(--text); }
  .status-quote { font-family: 'Space Grotesk', sans-serif; font-size: 22px; line-height: 1.3; color: var(--text); max-width: 280px; }
  .status-quote span { color: var(--accent); }
  
  .form-side { padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; background: var(--card); }
  .form-side h2 { font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 600; margin: 0 0 6px; }
  .form-side .sub { color: var(--text-dim); font-size: 14px; margin: 0 0 32px; }
  
  form { display: flex; flex-direction: column; gap: 18px; }
  label { font-size: 13px; color: var(--text-dim); display: block; margin-bottom: 6px; }
  input, select { width: 100%; background: var(--bg); border: 1px solid var(--line); color: var(--text); font-family: 'Inter', sans-serif; font-size: 14px; padding: 11px 12px; border-radius: 3px; outline: none; transition: 0.15s; }
  select { cursor: pointer; }
  select option { background: var(--bg); color: var(--text); }
  input:focus, select:focus { border-color: var(--accent); }
  
  button { margin-top: 8px; background: var(--accent); color: #1A1406; border: none; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; padding: 12px; border-radius: 3px; cursor: pointer; transition: 0.15s; }
  button:hover { filter: brightness(1.08); }
  
  .error { background: rgba(232, 105, 122, 0.1); border: 1px solid rgba(232, 105, 122, 0.4); color: var(--error); font-size: 13px; padding: 10px 12px; border-radius: 3px; margin-bottom: 18px; }
  .success { background: rgba(111, 207, 151, 0.1); border: 1px solid rgba(111, 207, 151, 0.4); color: var(--ok); font-size: 13px; padding: 10px 12px; border-radius: 3px; margin-bottom: 18px; }
  
  .foot { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-dim); }
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
          <div class="row"><span>session</span><span class="val">not started</span></div>
          <div class="row"><span>region</span><span class="val">local</span></div>
        </div>
      </div>
      <div class="status-quote">New here? <span>Create an account</span> to get a workspace or manage the system.</div>
    </div>
    <div class="form-side">
      <h2>Create Account</h2>
      <p class="sub">Register to get access to the system.</p>
      
      <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
      
      <form method="POST" autocomplete="off">
        <div>
          <label>Username</label>
          <input type="text" name="username" required autofocus>
        </div>
        <div>
          <label>Account Type</label>
          <select name="role" required>
            <option value="user">Standard User (Workspace Access)</option>
            <option value="admin">Administrator (System Management)</option>
          </select>
        </div>
        <div>
          <label>Password</label>
          <input type="password" name="password" required minlength="8">
        </div>
        <div>
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" required minlength="8">
        </div>
        <button type="submit">Create account</button>
      </form>
      
      <div class="foot">already have an account? <a href="login.php">log in</a></div>
    </div>
  </div>
</body>
</html>
