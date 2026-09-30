<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logged Out</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg: #10141B; --panel: #171C25; --card: #1A2029; --line: #2A313D; --text: #E7E9EC; --text-dim: #8B939E; --accent: #E8B563; }
  * { box-sizing: border-box; }
  body { margin: 0; background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .frame { width: 100%; max-width: 920px; display: grid; grid-template-columns: 1fr 1fr; background: var(--panel); border: 1px solid var(--line); min-height: 560px; }
  @media (max-width: 760px) { .frame { grid-template-columns: 1fr; min-height: auto; } .status-side { display: none; } }
  .status-side { padding: 40px 36px; border-right: 1px solid var(--line); display: flex; flex-direction: column; justify-content: space-between; background-image: linear-gradient(var(--line) 1px, transparent 1px), linear-gradient(90deg, var(--line) 1px, transparent 1px); background-size: 28px 28px; background-position: -1px -1px; }
  .status-header { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); }
  .status-header .dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: var(--text-dim); margin-right: 8px; }
  .status-list { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--text-dim); line-height: 2.1; }
  .status-list .row { display: flex; justify-content: space-between; border-bottom: 1px dashed var(--line); padding: 4px 0; }
  .status-list .val { color: var(--text); }
  .status-quote { font-family: 'Space Grotesk', sans-serif; font-size: 22px; line-height: 1.3; color: var(--text); max-width: 280px; }
  .status-quote span { color: var(--accent); }
  
  .form-side { padding: 48px 44px; display: flex; flex-direction: column; justify-content: center; background: var(--card); }
  .form-side .icon { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: var(--accent); font-family: 'JetBrains Mono', monospace; font-size: 18px; }
  .form-side h2 { font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 600; margin: 0 0 6px; }
  .form-side .sub { color: var(--text-dim); font-size: 14px; margin: 0 0 32px; }
  
  .btn { display: inline-block; text-align: center; background: var(--accent); color: #1A1406; text-decoration: none; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; padding: 12px; border-radius: 3px; transition: 0.15s; width: max-content; }
  .btn:hover { filter: brightness(1.08); }
  
  .foot { margin-top: 28px; font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-dim); }
</style>
</head>
<body>
  <div class="frame">
    <div class="status-side">
      <div>
        <div class="status-header"><span class="dot"></span>SESSION ENDED</div>
        <div class="status-list">
          <div class="row"><span>uptime</span><span class="val">held for a while</span></div>
          <div class="row"><span>session</span><span class="val">closed</span></div>
          <div class="row"><span>region</span><span class="val">local</span></div>
        </div>
      </div>
      <div class="status-quote">Your session has been <span>closed</span>. Sign back in when you're ready.</div>
    </div>
    <div class="form-side">
      <div class="icon">&#10003;</div>
      <h2>You've been logged out</h2>
      <p class="sub">Your session was ended and your data is no longer active.</p>
      <a href="login.php" class="btn">Back to login</a>
      <div class="foot">v1 &middot; internal use only</div>
    </div>
  </div>
</body>
</html>
