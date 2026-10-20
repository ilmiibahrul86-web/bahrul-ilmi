
<?php
session_start();

$pdo = null;
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=bahrulchat_dark;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    $dbError = 'Database belum siap. Buat database bahrulchat_dark terlebih dahulu.';
}

function out($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function memberCheck($pdo, $gid, $uid) {
    $s = $pdo->prepare(
        'SELECT 1 FROM group_members WHERE group_id=? AND user_id=?'
    );
    $s->execute([$gid, $uid]);
    return (bool)$s->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) out(['ok'=>false,'error'=>$dbError ?? 'Database tidak tersedia.']);

    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $name = trim(mb_substr($_POST['name'] ?? '', 0, 30));
        if ($name === '') out(['ok'=>false,'error'=>'Nama wajib diisi.']);

        $s = $pdo->prepare('INSERT INTO users(name) VALUES(?)');
        $s->execute([$name]);

        $_SESSION['uid'] = (int)$pdo->lastInsertId();
        $_SESSION['uname'] = $name;
        out(['ok'=>true,'name'=>$name]);
    }

    if ($action === 'me') {
        out(['ok'=>true,'name'=>$_SESSION['uname'] ?? null]);
    }

    if ($action === 'logout') {
        $_SESSION = [];
        session_destroy();
        out(['ok'=>true]);
    }

    if (empty($_SESSION['uid'])) {
        out(['ok'=>false,'error'=>'Silakan masuk terlebih dahulu.']);
    }

    $uid = (int)$_SESSION['uid'];

    if ($action === 'groups') {
        $s = $pdo->prepare(
            'SELECT g.* FROM chat_groups g
             JOIN group_members gm ON gm.group_id=g.id
             WHERE gm.user_id=? ORDER BY g.id DESC'
        );
        $s->execute([$uid]);
        out(['ok'=>true,'groups'=>$s->fetchAll()]);
    }

    if ($action === 'create') {
        $name = trim(mb_substr($_POST['name'] ?? '', 0, 50));
        if ($name === '') out(['ok'=>false,'error'=>'Nama grup wajib diisi.']);

        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $s = $pdo->prepare('SELECT id FROM chat_groups WHERE code=?');
            $s->execute([$code]);
        } while ($s->fetch());

        $pdo->beginTransaction();
        $s = $pdo->prepare(
            'INSERT INTO chat_groups(name,code,owner_id) VALUES(?,?,?)'
        );
        $s->execute([$name,$code,$uid]);
        $gid = (int)$pdo->lastInsertId();

        $s = $pdo->prepare(
            'INSERT INTO group_members(group_id,user_id) VALUES(?,?)'
        );
        $s->execute([$gid,$uid]);
        $pdo->commit();

        out(['ok'=>true,'code'=>$code]);
    }

    if ($action === 'join') {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $s = $pdo->prepare('SELECT id FROM chat_groups WHERE code=?');
        $s->execute([$code]);
        $g = $s->fetch();

        if (!$g) out(['ok'=>false,'error'=>'Kode grup tidak ditemukan.']);

        $s = $pdo->prepare(
            'INSERT IGNORE INTO group_members(group_id,user_id) VALUES(?,?)'
        );
        $s->execute([(int)$g['id'],$uid]);
        out(['ok'=>true]);
    }

    if ($action === 'messages') {
        $gid = (int)($_POST['group_id'] ?? 0);
        if (!memberCheck($pdo,$gid,$uid)) {
            out(['ok'=>false,'error'=>'Kamu bukan anggota grup.']);
        }

        $s = $pdo->prepare(
            'SELECT m.id,m.user_id,m.message,u.name,
             DATE_FORMAT(m.created_at,"%H:%i") AS waktu
             FROM messages m JOIN users u ON u.id=m.user_id
             WHERE m.group_id=? ORDER BY m.id DESC LIMIT 100'
        );
        $s->execute([$gid]);
        out(['ok'=>true,'messages'=>array_reverse($s->fetchAll())]);
    }

    if ($action === 'send') {
        $gid = (int)($_POST['group_id'] ?? 0);
        $message = trim(mb_substr($_POST['message'] ?? '',0,3000));

        if (!memberCheck($pdo,$gid,$uid)) {
            out(['ok'=>false,'error'=>'Kamu bukan anggota grup.']);
        }
        if ($message === '') out(['ok'=>false,'error'=>'Pesan kosong.']);

        $s = $pdo->prepare(
            'INSERT INTO messages(group_id,user_id,message) VALUES(?,?,?)'
        );
        $s->execute([$gid,$uid,$message]);
        out(['ok'=>true]);
    }

    out(['ok'=>false,'error'=>'Perintah tidak dikenal.']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BahrulChat | Dark Premium</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');

:root {
 --bg:#080b0d; --panel:#111719; --panel2:#182120;
 --line:#293735; --green:#b8ff35; --text:#f3f7f4; --muted:#879791;
}
*{box-sizing:border-box}
body{
 margin:0;min-height:100vh;background:
 radial-gradient(ellipse at 15% 0%,#17291b 0,transparent 38%),var(--bg);
 color:var(--text);font-family:'DM Sans',sans-serif
}
button,input{font:inherit}
button{cursor:pointer;transition:.2s}
button:hover{filter:brightness(1.1);box-shadow:0 0 18px #b8ff3520}
.hidden{display:none!important}
#login{min-height:100vh;display:grid;place-items:center;padding:20px}
.login-card{
 width:min(430px,100%);padding:32px;background:linear-gradient(145deg,#141d1e,#0e1314);
 border:1px solid #34413c;border-radius:25px;box-shadow:0 25px 90px #0008;
 animation:rise .5s ease
}
.logo{font:700 25px 'Space Grotesk',sans-serif;letter-spacing:-1px}
.logo b{color:var(--green)}
.mark{display:inline-grid;place-items:center;background:var(--green);color:#101607;
 width:43px;height:43px;border-radius:14px;margin-right:9px}
.eyebrow{font-size:10px;letter-spacing:2px;color:var(--green);font-weight:700;margin-top:28px}
h1{font:700 38px/1.12 'Space Grotesk',sans-serif;letter-spacing:-1.5px;margin:18px 0 12px}
h1 span{color:var(--green)}
p{color:var(--muted);line-height:1.7;font-size:14px}
.field{width:100%;padding:14px 15px;background:#080d0e;border:1px solid #33413d;
 border-radius:12px;color:white;outline:none;margin:10px 0}
.field:focus{border-color:var(--green);box-shadow:0 0 0 3px #b8ff3518}
.btn{border:0;border-radius:12px;background:var(--green);color:#101607;font-weight:800;padding:13px 16px}
.wide{width:100%;margin-top:8px}
#app{height:100dvh;padding:16px}
.layout{height:100%;max-width:1450px;margin:auto;display:grid;grid-template-columns:300px minmax(0,1fr);
 border:1px solid var(--line);border-radius:22px;overflow:hidden;background:var(--bg);animation:rise .4s}
.sidebar{display:flex;flex-direction:column;min-width:0;background:#101617;border-right:1px solid var(--line)}
.side-head{padding:20px;border-bottom:1px solid var(--line)}
.status{font-size:12px;color:var(--muted);margin-top:17px}
.dot{display:inline-block;width:7px;height:7px;background:var(--green);border-radius:50%;box-shadow:0 0 9px var(--green);margin-right:6px}
.actions{display:flex;gap:8px;margin-top:16px}
.actions button{flex:1;border-radius:10px;padding:11px 5px;border:1px solid #35423e;background:#192221;color:white;font-size:12px}
.actions .btn{background:var(--green);color:#101607;border-color:var(--green)}
.search{margin:14px 12px;background:#192221;border:1px solid transparent;border-radius:10px;padding:12px;color:white;outline:none}
.search:focus{border-color:var(--green)}
.group-list{flex:1;overflow:auto;padding:0 9px}
.group{padding:12px;display:flex;gap:11px;align-items:center;border:1px solid transparent;border-radius:12px;margin-bottom:5px;cursor:pointer}
.group:hover,.group.active{background:#1b2921;border-color:#3a5030}
.avatar{width:42px;height:42px;display:grid;place-items:center;flex:none;background:#26382a;border-radius:13px;color:var(--green);font-weight:800}
.group-name{font-size:13px;font-weight:700;overflow-wrap:anywhere}
.me{padding:15px;border-top:1px solid var(--line);font-size:12px;color:var(--muted)}
.me button{float:right;background:transparent;border:0;color:var(--green)}
.main{display:flex;flex-direction:column;min-width:0;background:radial-gradient(ellipse at 80% 0%,#15231a,transparent 40%),#0b1011}
.chat-head{display:flex;align-items:center;gap:12px;padding:17px 22px;border-bottom:1px solid var(--line);background:#0d1314cc}
.chat-head h2{font:700 17px 'Space Grotesk',sans-serif;margin:0 0 5px}
.chat-head small{color:var(--muted)}
.messages{flex:1;overflow-y:auto;padding:22px;display:flex;flex-direction:column;gap:13px}
.empty{margin:auto;text-align:center;color:var(--muted);line-height:1.8}
.empty strong{display:block;color:var(--green);font-size:38px;text-shadow:0 0 22px #b8ff3540}
.message{max-width:min(75%,540px);align-self:flex-start;animation:rise .2s}
.message.mine{align-self:flex-end}
.sender{font-size:11px;color:var(--green);margin:0 0 5px 4px}
.bubble{padding:12px 14px;background:#172120;border:1px solid #2b3936;border-radius:5px 15px 15px 15px;white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.55;font-size:13px}
.mine .bubble{background:#20301f;border-color:#3b5030;border-radius:15px 5px 15px 15px}
.time{font-size:10px;color:var(--muted);text-align:right;margin-top:6px}
.composer{display:flex;gap:9px;padding:14px;border-top:1px solid var(--line);background:#0d1314}
.composer input{flex:1;min-width:0;background:#172020;border:1px solid #2b3937;border-radius:12px;padding:13px;color:white;outline:none}
.composer input:focus{border-color:var(--green)}
.composer button{border:0;background:var(--green);color:#101607;font-weight:800;border-radius:12px;padding:0 18px}
.notice{font-size:10px;color:#62716b;margin:0 17px 13px}
#toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);padding:12px 18px;border-radius:12px;background:var(--green);color:#101607;font-weight:800;display:none;z-index:10;max-width:90%}
@keyframes rise{from{opacity:0;transform:translateY(9px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:700px){
 #app{padding:0}.layout{border:0;border-radius:0;grid-template-columns:1fr}
 .sidebar{height:100dvh}.main{display:none}
 .layout.chat-open .sidebar{display:none}
 .layout.chat-open .main{display:flex;height:100dvh}
 .side-head{padding:16px}.chat-head{padding:13px}
 .messages{padding:15px 10px}.message{max-width:90%}
 .composer{padding:10px}.composer button{padding:0 13px}
 .login-card{padding:26px}
}
</style>
</head>
<body>
<section id="login">
 <div class="login-card">
  <div class="logo"><span class="mark">B</span>Bahrul<b>Chat</b></div>
  <div class="eyebrow">PRIVATE SPACE · CONNECTED</div>
  <h1>Chat your way.<br><span>Stay connected.</span></h1>
  <p>Ruang ngobrol modern untuk kamu dan teman-teman. Buat grup, bagikan kode, lalu mulai percakapan.</p>
  <input id="loginName" class="field" maxlength="30" placeholder="Masukkan nama kamu">
  <button class="btn wide" onclick="login()">Masuk ke BahrulChat ↗</button>
 </div>
</section>

<section id="app" class="hidden">
 <div id="layout" class="layout">
  <aside class="sidebar">
   <div class="side-head">
    <div class="logo"><span class="mark">B</span>Bahrul<b>Chat</b></div>
    <div class="status"><span class="dot"></span>Online space</div>
    <div class="actions">
     <button class="btn" onclick="createGroup()">＋ Buat grup</button>
     <button onclick="joinGroup()">↗ Gabung</button>
    </div>
   </div>
   <input id="search" class="search" placeholder="⌕ Cari grup..." oninput="renderGroups()">
   <div id="groupList" class="group-list"></div>
   <div class="me"><span class="dot"></span><span id="userName"></span><button onclick="logout()">Keluar ↗</button></div>
  </aside>
  <main class="main">
   <header class="chat-head">
    <button class="btn" onclick="backGroups()">←</button>
    <div class="avatar" id="headAvatar">B</div>
    <div><h2 id="headName">Pilih grup</h2><small id="headSub">Pilih grup untuk mulai chat</small></div>
   </header>
   <div id="messages" class="messages"><div class="empty"><strong>✳</strong>Selamat datang di ruangmu.<br>Pilih grup atau buat grup baru untuk mulai.</div></div>
   <form class="composer" onsubmit="sendMessage(event)">
    <input id="messageInput" maxlength="3000" placeholder="Tulis pesan..." autocomplete="off">
    <button>Kirim ↗</button>
   </form>
   <div class="notice">ENTER UNTUK MENGIRIM · DARK PREMIUM</div>
  </main>
 </div>
</section>
<div id="toast"></div>

<script>
let userName='',groups=[],active=null,timer=null,lastSig='';
const $=id=>document.getElementById(id);
function toast(t){$('toast').textContent=t;$('toast').style.display='block';clearTimeout(window.toastTimer);window.toastTimer=setTimeout(()=>$('toast').style.display='none',2800)}
function esc(s){return String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}
async function api(action,data={}){
 const fd=new FormData();fd.append('action',action);
 for(const k in data)fd.append(k,data[k]);
 const r=await fetch('index.php',{method:'POST',body:fd});
 const j=await r.json();
 if(!j.ok)throw Error(j.error||'Terjadi kesalahan');
 return j;
}
async function login(){
 const name=$('loginName').value.trim();
 if(!name)return toast('Masukkan nama kamu.');
 try{
  const j=await api('login',{name});userName=j.name;
  $('login').classList.add('hidden');$('app').classList.remove('hidden');
  $('userName').textContent=userName;loadGroups();
 }catch(e){toast(e.message)}
}
async function logout(){try{await api('logout')}catch(e){}location.reload()}
async function loadGroups(){try{const j=await api('groups');groups=j.groups;renderGroups()}catch(e){toast(e.message)}}
function renderGroups(){
 const q=$('search').value.toLowerCase();$('groupList').innerHTML='';
 const list=groups.filter(g=>g.name.toLowerCase().includes(q));
 if(!list.length){$('groupList').innerHTML='<p style="padding:15px;color:#879791;font-size:13px">Belum ada grup. Buat grup atau gabung dengan kode.</p>';return}
 list.forEach(g=>{
  const d=document.createElement('div');d.className='group'+(active&&active.id==g.id?' active':'');
  d.innerHTML='<div class="avatar">'+esc(g.name[0].toUpperCase())+'</div><div class="group-name">'+esc(g.name)+'</div>';
  d.onclick=()=>openGroup(g);$('groupList').appendChild(d);
 });
}
async function createGroup(){
 const name=prompt('Masukkan nama grup:');if(!name||!name.trim())return;
 try{
  const j=await api('create',{name:name.trim()});
  toast('Grup berhasil dibuat! Kode: '+j.code);await loadGroups();
  const g=groups.find(x=>x.code===j.code);if(g)openGroup(g);
 }catch(e){toast(e.message)}
}
async function joinGroup(){
 const code=prompt('Masukkan kode grup:');if(!code)return;
 try{await api('join',{code:code.trim()});toast('Berhasil bergabung!');await loadGroups()}
 catch(e){toast(e.message)}
}
async function openGroup(g){
 active=g;$('layout').classList.add('chat-open');
 $('headName').textContent=g.name;$('headSub').textContent='Kode undangan: '+g.code;
 $('headAvatar').textContent=g.name[0].toUpperCase();lastSig='';
 renderGroups();await loadMessages(true);
 clearInterval(timer);timer=setInterval(()=>loadMessages(false),2500);
}
function backGroups(){$('layout').classList.remove('chat-open');clearInterval(timer)}
async function loadMessages(force){
 if(!active)return;
 try{
  const j=await api('messages',{group_id:active.id});
  const sig=j.messages.map(m=>m.id).join(',');
  if(!force&&sig===lastSig)return;
  const box=$('messages');box.innerHTML='';
  if(!j.messages.length)box.innerHTML='<div class="empty"><strong>✳</strong>Awal percakapan baru.<br>Jadilah yang pertama mengirim pesan.</div>';
  j.messages.forEach(m=>{
   const mine=Number(m.user_id)===Number(<?php echo json_encode($_SESSION['uid']??0); ?>);
   const d=document.createElement('div');d.className='message'+(mine?' mine':'');
   d.innerHTML=(mine?'':'<div class="sender">'+esc(m.name)+'</div>')+
    '<div class="bubble">'+esc(m.message)+'<div class="time">'+esc(m.waktu)+'</div></div>';
   box.appendChild(d);
  });
  box.scrollTop=box.scrollHeight;lastSig=sig;loadGroups();
 }catch(e){console.log(e)}
}
async function sendMessage(e){
 e.preventDefault();if(!active)return toast('Pilih grup terlebih dahulu.');
 const input=$('messageInput'),message=input.value.trim();if(!message)return;
 try{await api('send',{group_id:active.id,message});input.value='';await loadMessages(true)}
 catch(err){toast(err.message)}
}
$('loginName').addEventListener('keydown',e=>{if(e.key==='Enter')login()});
(async()=>{try{const j=await api('me');if(j.name){userName=j.name;$('login').classList.add('hidden');$('app').classList.remove('hidden');$('userName').textContent=userName;loadGroups()}}catch(e){}})();
</script>
</body>
</html>
