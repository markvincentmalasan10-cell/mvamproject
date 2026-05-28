<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title')</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="{{ asset('js/app.js') }}"></script>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins', sans-serif;
    background:#f4f7fb;
    color:#1e293b;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

header{
    background:#0f172a;
    box-shadow:0 4px 16px rgba(15, 23, 42, 0.18);
}

.header-container{
    max-width:1100px;
    margin:0 auto;
    padding:18px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
}

.header-container h1{
    color:#ffffff;
    font-size:24px;
    font-weight:600;
}

nav{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

nav a{
    text-decoration:none;
    color:#cbd5e1;
    font-size:15px;
    font-weight:500;
    padding:8px 14px;
    border-radius:8px;
    transition:background-color 0.2s ease, color 0.2s ease;
}

nav .inline-form{
    display:inline-flex;
}

nav .nav-logout{
    padding:8px 14px;
    border-radius:8px;
    background:transparent;
    color:#cbd5e1;
    font-size:15px;
    font-weight:500;
    line-height:normal;
    box-shadow:none;
}

nav a:hover,
nav a.active,
nav .nav-logout:hover{
    background:#38bdf8;
    color:#0f172a;
}

main{
    flex:1;
    max-width:1100px;
    width:100%;
    margin:0 auto;
    padding:32px 20px;
}

body.auth-page{
    position:relative;
    overflow:hidden;
    background:#022f34;
}

body.auth-page main{
    position:relative;
    z-index:1;
    max-width:1200px;
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:100vh;
    padding:12px 16px;
}

body.auth-page::before{
    content:"";
    position:fixed;
    inset:0;
    background:linear-gradient(135deg, transparent 0 48%, rgba(4, 24, 39, 0.45) 48% 100%);
    pointer-events:none;
}

.content-container{
    background:#ffffff;
    border-radius:16px;
    padding:28px;
    box-shadow:0 10px 30px rgba(15, 23, 42, 0.08);
}

body.auth-page .content-container{
    background:transparent;
    box-shadow:none;
    padding:0;
    width:100%;
}

.page-title{
    font-size:28px;
    font-weight:600;
    margin-bottom:18px;
    color:#0f172a;
}

.page-text{
    color:#475569;
    line-height:1.7;
}

.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin:18px 0;
    flex-wrap:wrap;
}

.success-message{
    margin-bottom:16px;
    padding:12px 16px;
    border-radius:10px;
    background:#dcfce7;
    color:#166534;
}

.error-message{
    margin-bottom:16px;
    padding:12px 16px;
    border-radius:10px;
    background:#fee2e2;
    color:#991b1b;
}

.btn,
button,
input[type="submit"]{
    display:inline-block;
    padding:10px 16px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:#ffffff;
    text-decoration:none;
    font-size:14px;
    font-weight:500;
    cursor:pointer;
    transition:opacity 0.2s ease, transform 0.2s ease;
}

.btn:hover,
button:hover,
input[type="submit"]:hover{
    opacity:0.92;
    transform:translateY(-1px);
}

.btn-secondary{
    background:#0f172a;
}

.btn-danger{
    background:#dc2626;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:16px;
    overflow:hidden;
    border-radius:12px;
}

table thead{
    background:#e2e8f0;
}

table th,
table td{
    padding:14px 16px;
    text-align:left;
    border-bottom:1px solid #e2e8f0;
}

table tbody tr:nth-child(even){
    background:#f8fafc;
}

table tbody tr:hover{
    background:#eff6ff;
}

.action-links{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
}

.icon-actions{
    display:flex;
    gap:8px;
    align-items:center;
}

.icon-btn{
    width:40px;
    height:40px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border:none;
    border-radius:12px;
    text-decoration:none;
    cursor:pointer;
    transition:transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    color:#ffffff;
}

.icon-btn svg{
    width:18px;
    height:18px;
    stroke:currentColor;
}

.icon-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(15, 23, 42, 0.16);
}

.icon-view{
    background:#2563eb;
}

.icon-edit{
    background:#0f172a;
}

.icon-delete{
    background:#dc2626;
}

form.inline-form{
    display:inline-block;
}

.icon-form{
    margin:0;
}

.form-card{
    max-width:760px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.form-group{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.form-group.full-width{
    grid-column:1 / -1;
}

.form-group label{
    font-weight:500;
    color:#0f172a;
}

.form-group input{
    padding:12px 14px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group select{
    padding:12px 14px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56, 189, 248, 0.16);
}

.form-group select:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56, 189, 248, 0.16);
}

.form-actions{
    margin-top:20px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.dashboard-panel{
    margin-top:14px;
    padding:22px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:8px;
}

.dashboard-panel h3{
    margin-bottom:8px;
    color:#0f172a;
    font-size:22px;
}

.student-profile-summary{
    display:flex;
    align-items:center;
    gap:18px;
    flex-wrap:wrap;
}

.student-profile-image{
    width:140px;
    height:140px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #cbd5e1;
    background:#f8fafc;
}

.dashboard-eyebrow{
    margin-bottom:8px;
    color:#2563eb;
    font-weight:600;
}

.login-shell{
    width:100%;
    max-width:620px;
    margin:0 auto;
}

.login-panel{
    padding:18px 16px;
    background:transparent;
}

.login-panel-top{
    margin-bottom:18px;
    text-align:center;
}

.login-panel h3{
    font-size:34px;
    line-height:1;
    color:#f8fafc;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.02em;
}

.login-subtext{
    margin-top:6px;
    color:rgba(226, 232, 240, 0.72);
    line-height:1.5;
    font-size:12px;
}

.login-form{
    display:grid;
    gap:16px;
}

.login-field{
    position:relative;
}

.login-field input{
    width:100%;
    height:64px;
    padding:0 78px 0 90px;
    border:none;
    border-radius:999px;
    background:rgba(126, 153, 161, 0.62);
    color:#052f35;
    font-size:18px;
    font-weight:500;
    outline:none;
    transition:background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.login-field input:focus{
    background:rgba(148, 173, 180, 0.78);
    box-shadow:0 0 0 4px rgba(255, 255, 255, 0.12);
    transform:translateY(-1px);
}

.login-field input::placeholder{
    color:#0d3640;
}

.login-field-icon{
    position:absolute;
    top:50%;
    width:60px;
    height:60px;
    border-radius:50%;
    background:#f8f7f5;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#082f39;
    transform:translateY(-50%);
    box-shadow:0 10px 30px rgba(0, 0, 0, 0.18);
}

.login-field-icon svg{
    width:26px;
    height:26px;
    stroke:currentColor;
}

.login-field-icon.left{
    left:0;
}

.login-field-icon.right{
    right:0;
}

.login-submit{
    width:100%;
    margin-top:8px;
    padding:14px 16px;
    border:none;
    border-radius:999px;
    background:#f8f7f5;
    color:#082f39;
    font-size:22px;
    font-weight:700;
    letter-spacing:0.02em;
    text-transform:uppercase;
    box-shadow:0 16px 32px rgba(0, 0, 0, 0.16);
}

.login-submit:hover{
    opacity:1;
    transform:translateY(-2px);
}

.login-helper{
    margin-top:10px;
    color:rgba(226, 232, 240, 0.68);
    font-size:12px;
    text-align:center;
}

.auth-flash{
    margin-bottom:14px;
    padding:12px 16px;
    border-radius:18px;
    font-size:13px;
    border:1px solid rgba(255, 255, 255, 0.1);
}

.auth-flash.success{
    background:rgba(34, 197, 94, 0.14);
    color:#dcfce7;
}

.auth-flash.info{
    background:rgba(56, 189, 248, 0.14);
    color:#e0f2fe;
}

body.auth-page .error-message{
    background:rgba(239, 68, 68, 0.16);
    color:#fee2e2;
    border:1px solid rgba(254, 202, 202, 0.14);
    border-radius:18px;
    padding:12px 16px;
}

.details-list{
    display:grid;
    gap:12px;
    margin-top:18px;
}

.detail-item{
    padding:14px 16px;
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:12px;
}

.detail-item strong{
    display:block;
    margin-bottom:4px;
    color:#0f172a;
}

.pagination-wrapper{
    margin-top:20px;
}

.pagination{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.pagination .page-item{
    list-style:none;
}

.pagination .page-link,
.pagination span{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:38px;
    height:38px;
    padding:0 12px;
    border-radius:8px;
    text-decoration:none;
    background:#ffffff;
    color:#1e293b;
    border:1px solid #cbd5e1;
}

.pagination .active span{
    background:#2563eb;
    color:#ffffff;
    border-color:#2563eb;
}

footer{
    background:#0f172a;
    color:#cbd5e1;
    text-align:center;
    padding:16px;
    font-size:14px;
    margin-top:20px;
}

@media(max-width:768px){
    .header-container{
        flex-direction:column;
        align-items:flex-start;
    }

    .content-container{
        padding:20px;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .login-shell{
        max-width:100%;
    }

    .login-panel{
        padding:14px 4px;
    }

    .login-panel h3{
        font-size:28px;
    }

    .login-field input{
        height:58px;
        padding:0 74px 0 82px;
        font-size:17px;
    }

    .login-field-icon{
        width:54px;
        height:54px;
    }

    .login-field-icon svg{
        width:22px;
        height:22px;
    }

    .login-submit{
        font-size:18px;
        padding:13px 16px;
    }
}
</style>
</head>

<body class="@yield('body-class')">

<header>
@section('Header')
<div class="header-container">
    <h1>Student Management System</h1>

    <nav>
        @if(session('user_account_id'))
            <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ url('/profile') }}" class="{{ request()->is('profile') ? 'active' : '' }}">Profile</a>
            @if(session('user_role') === 'admin')
                <a href="{{ url('/student') }}" class="{{ request()->is('student') || request()->is('student/*') ? 'active' : '' }}">Students</a>
                <a href="{{ route('students.export.excel') }}" onclick="return confirm('Download an Excel file that contains all students?')">Excel</a>
                <a href="{{ route('students.export.pdf') }}" onclick="return confirm('Download a PDF file that contains all students?')">PDF</a>
                <a href="{{ url('/teacher') }}" class="{{ request()->is('teacher') || request()->is('teacher/*') ? 'active' : '' }}">Teachers</a>
                <a href="{{ url('/users') }}" class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">Users</a>
                <a href="{{ route('degrees.index') }}" class="{{ request()->is('degrees') || request()->is('degrees/*') ? 'active' : '' }}">Degrees</a>
                <a href="{{ url('/aboutus') }}" class="{{ request()->is('aboutus') ? 'active' : '' }}">About</a>
                <a href="/demo">Demo</a>
            @endif
            <a href="/logout" class="nav-logout">Logout</a>
        @else
            <a href="{{ url('/login') }}" class="{{ request()->is('login') ? 'active' : '' }}">Login</a>
        @endif
    </nav>
</div>
@show
</header>

<main>
    <div class="content-container">
        @yield('Content')
    </div>
</main>

<footer>
@section('Footer')
<p>&copy; {{ date('Y') }} Student Management System | All Rights Reserved</p>
@show
</footer>

</body>
</html>
