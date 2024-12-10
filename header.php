<header>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="brand pull-left" href="dashboard.php?almacen=CD11">
                    <img style="    width: 90%;    position: relative; " src="img/logo-white.svg"/>
                    </a>
                <ul class="nav navbar-nav user_menu pull-right">                 
                    <li ><a href="dashboard.php" >Dashboard</a></li>                                
                    <li class="divider-vertical hidden-sm hidden-xs"></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="img/user_avatar.png" alt="" class="user_avatar"><?php echo $_SESSION['user'] ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu dropdown-menu-right">
<!--                            <li><a href="user_profile.html">My Profile</a></li>
                            <li><a href="javascript:void(0)">Another action</a></li>-->
                            <!--<li class="divider"></li>-->
                            <li><a onclick="closeSession()" href="javascript:void(0)"><i class="glyphicon glyphicon-log-out"></i> Cerrar sesi&oacute;n</a></li>
                        </ul>
                    </li>                                                                                                                                                                                                                 
                </ul>
            </div>
        </div>
    </nav>
</header>
