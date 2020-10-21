<div class="left_col scroll-view" style="width: 100%">
	<div class="navbar nav_title" style="border: 0;">
		<a href="{!! URL::to('/') !!}" class="site_title"></i> <span>KNUST ATSS v1.0</span></a>
	</div>

	<div class="clearfix"></div>

	<!-- menu profile quick info -->
	<div class="profile clearfix">
		<div class="row">
			<img id="profile-pic" src="{{  URL::asset('/images/logo.jpeg') }}" class="img-circle profile_img"
        style="height: 80px; width: 70px">
		</div>

		<div class="row profile_info">
			<h2 class="text-center">Hi, {{ Auth::user()->name }}</h2>
		</div>

		<div class="clearfix"></div>
	</div>

	<br />

	<!-- sidebar menu -->
	<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
		<div class="menu_section">
			<ul class="nav side-menu">
			    <?php $page = Request::segment(1); ?>
                <li class="menu-link {{ ($page == 'dashboard') ? 'active' : '' }}">
                    <a href="/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a>
				</li>

                <li class="menu-link {{ ($page == 'timetables') ? 'active' : '' }}">
                    <a href="/timetables"><i class="fa fa-calendar"></i> Timetables</a>
                </li>

                <li class="menu-link {{ ($page == 'buildings') ? 'active' : '' }}">
                    <a href="/blocks"><i class="fa fa-building"></i> Buildings</a>
                </li>

                <li class="menu-link {{ ($page == 'rooms') ? 'active' : '' }}">
                    <a href="/rooms"><i class="fa fa-home"></i> Rooms</a>
                </li>

                <li class="menu-link {{ ($page == 'courses') ? 'active' : '' }}">
                    <a href="/courses"><i class="fa fa-book"></i> Courses</a>
                </li>
                <li class="menu-link {{ ($page == 'professors') ? 'active' : '' }}">
                    <a href="/professors"><i class="fa fa-graduation-cap"></i> Lecturers</a>
                </li>
                <li class="menu-link {{ ($page == 'classes') ? 'active' : '' }}">
                    <a href="/classes"><i class="fa fa-users"></i> Classes</a>
                </li>
                <li class="menu-link {{ ($page == 'timeslots') ? 'active' : '' }}">
                    <a href="/timeslots"><i class="fa fa-clock-o"></i> Periods</a>
                </li>
                <li class="menu-link {{ ($page == 'reports') ? 'active' : '' }}">
                    <a href="/reports"><i class="fa fa-file"></i> Reports</a>
                </li>
			</ul>
		</div>

		<div class="menu_section">
			<ul class="nav side-menu">
                <li class="menu-link {{ ($page == 'my_account') ? 'active' : '' }}">
                    <a href="/my_account"><i class="fa fa-user"></i> My Account</a>
                </li>

                <li class="menu-link">
                    <a href="/logout"><i class="fa fa-sign-out"></i> Log Out</a>
                </li>
            </ul>
		</div>
	</div>
</div>