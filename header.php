<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="/"><img src='assets/img/logo-sm.png'></a><button
            class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
            <i class="fas fa-bars"></i></button><!-- Navbar Search-->
                <form class="d-none d-md-inline-block form-inline ml-auto mr-auto">
                    <!--
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    -->
                </form>
                <marquee><b class="text-white">WELCOME TO <?=strtoupper($config->name) ?></b></marquee>
    
        <!-- Navbar-->
        <ul class="navbar-nav float-right">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-danger" id="userDropdown" href="#" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="keylock.php">Change Password</a>
                    <a class="dropdown-item" href="keylock.php">Staff Mail</a>
                    <div class="dropdown-divider"></div>
                   
                    <a class="dropdown-item" href="logout.php">Logout</a>
                    <!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '46711ff3c6e26c2d350a87aacc78043a9008a801';
window.smartsupp||(function(d) {
 var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
 s=d.getElementsByTagName('script')[0];c=d.createElement('script');
 c.type='text/javascript';c.charset='utf-8';c.async=true;
 c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
                </div>
            </li>
        </ul>
    </nav>
    