<section class="inner-navbar">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<ul>
					<li class="{{ Request::is('about') ? 'active': '' }}">
						<a title="About" href="/about">About</a>
					</li>
					<li class="{{ Request::is('careers') ? 'active': '' }}">
						<a title="Careers" href="/careers">Careers</a>
					</li>
					<!-- <li class="{{ Request::is('our-customers') ? 'active': '' }}">
						<a title="Customers" href="/our-customers">Customers</a>
					</li> -->
					<li class="{{ Request::is('faq') ? 'active': '' }}">
						<a title="FAQ" href="/faq">FAQ</a>
					</li>
					<li class="{{ Request::is('contact') ? 'active': '' }}">
						<a title="Contact Us" href="/contact">Contact Us</a>
					</li>
					<li class="{{ Request::is('terms-of-service') ? 'active': '' }}">
						<a title="Terms of Service" href="/terms-of-service">Terms of Service</a>
					</li>
					<li class="{{ Request::is('privacy') ? 'active': '' }}">
						<a title="Privacy Policy" href="/privacy">Privacy Policy</a>
					</li>
					@yield('nav-items')
				</ul>
			</div>
		</div>
	</div>
</section>
