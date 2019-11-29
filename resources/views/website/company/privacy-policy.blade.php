@extends('website.layout')

@section('title', 'Privacy Policy')

@section('meta')
    <meta name="title" content="Privacy Policy | Lootly">
    <meta name="description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta name="keywords" content="loyalty, referrals, rewards, ecommerce, rewards program">
    <meta property="og:title" content="Privacy Policy | Lootly">
    <meta property="og:image" content="https://s3.amazonaws.com/lootly-website-assets/img/logo-black.png">
    <meta property="og:url" content="{{ url('/privacy') }}">
    <meta property="og:description" content="Build customer loyalty, increase retention, and scale your brand. It is all possible with Lootly.">
    <meta property="og:site_name" content="Lootly.io">
@endsection

@section('nav-items')
	<li class="{{ Request::is('press') ? 'active': '' }}">
		<a title="Press" href="/press">Press</a>
	</li>
@endsection

@section('content')
	<section class="head-section">
		<h1>Privacy Policy</h1>
	</section>

    @include('website.company._nav')

	<section class="policy sec-border-bottom md-sec">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="paragraph">
						<p>The following information explains the information that Lootly, Inc. collects and how that information may be used by Lootly, Inc. (hereinafter referred to as “Lootly”). Lootly reserves the right to update this policy at any time and without any notice to users of the Lootly website.  Additionally, the information contained in this privacy policy applies to the following Lootly products:  websites, computer software, applications, and/or services provided by and/or purchased from Lootly. (Collectively hereinafter referred to as “products”).</p>
						<p class="bold">Acceptance by Use:  Please note that by using our products, you are agreeing to the policies contained within this policy, as Lootly as the terms and conditions applicable to our products which are incorporated by reference herein.</p>
					</div>

					<div class="paragraph">
						<h4><i class="int">I.</i> Information from Website Browsers</h4>
						<p>When you use our products, regardless of whether or not you have a registered account, Lootly may collect information from your visit.  Most Websites collect the same type of information.  Also, please note that our Website uses common internet technologies, such as cookies, to obtain information.  Here is the information that Lootly may collect from you during your visit:</p>
						<ul>
							<li>The type of operating system and/or browser that you are using to visit our 			website or products;</li>
							<li>Your language preference;</li>
							<li>The website that referred you to us (if applicable);</li>
							<li>The date and time of your visit</li>
							<li>Your IP address (please note that this could be potentially personally-identifiable information).</li>
							<li>Your activity on our website or products;</li>
						</ul>
					</div>

					<div class="paragraph">
						<h4><i class="int">II.</i> Information from Users with Registered Accounts</h4>
						<p>In addition to the information collected in subsection I above, when you register for an account with Lootly, Lootly will ask you to provide some additional personal information.  Lootly may collect this information via the internet, telephone, in-person, or other methods.  The personal information that Lootly may collect includes:</p>
						<ul>
							<li>Your full name.</li>
							<li>Your e-mail address (please note that by providing your e-mail address you are 	consenting to Lootly contacting you through that method for purposes including 	marketing).</li>
							<li>Your billing address.</li>
							<li>Your payment information (i.e. credit card number, etc.), including CSV numbers or other fraud-reducing details (please note that Lootly does not store this information, and is instead processed automatically by our payment provider – Stripe. For any questions related to how cards are processed, please contact Stripe directly.).</li>
							<li>Your username and password (created by you).</li>
						</ul>
					</div>

					<div class="paragraph">
						<h4><i class="int">III.</i> How Lootly Use the Information Collected</h4>
						<p>Once Lootly collect any of the above described information from you, Lootly may use that information for one or more of the following reasons:</p>
						<ul>
							<li>Communicating with you in general, including but not limited to the following purposes:
								<ul>
									<li>Account-related questions and/or concerns;</li>
									<li>Billing purposes including:
										<ul>
											<li>Processing purchases;</li>
											<li>Tracking transactions;</li>
											<li>Fraud prevention</li>
										</ul>
									</li>
									<li>Marketing purposes</li>
								</ul>
							</li>
							<li>Providing services associated with our products as requested by you;</li>
							<li>Analyzing our business statistics;</li>
							<li>Personalizing your Lootly experience;</li>
							<li>Complying with legal and/or governmental requests and/or requirements;</li>
							<li>Providing and/or delivering information to Third Parties.</li>
						</ul>
					</div>

					<div class="paragraph">
						<h4><i class="int">IV.</i> Disclosure of Information to Third Parties</h4>
						<p>Rest assured, Lootly does not sell, transfer, or otherwise disclose your personal information to an unaffiliated third party. Notwithstanding, sometimes an event occurs or a situation arises whereby Lootly must disclose or otherwise provide your information to a third party. These situations include but are not limited to:</p>
						<ul>
							<li>Providing payment information to payment processing companies including but not limited to credit card processing companies;</li>
							<li>Providing information as requested and/or required by legal and/or governmental authorities;</li>
							<li>Fraud prevention</li>
						</ul>
						<p>In accordance with the applicable laws of the United States of America, Lootly will not disclose your consumer information without your written consent, except as necessary to comply with a court order or other legal and/or governmental request and/or requirement, and/or to complete the billing/payment process.  By using Lootly products, you are consenting to the disclosure of your information for the above stated purposes.</p>
					</div>

					<div class="paragraph">
						<h4><i class="int">V.</i> Protecting Your Information</h4>
						<p>Lootly engages in appropriate and standard industry security protocols to ensure protection of your personal information.  Lootly cannot guarantee, nor does Lootly warrant, absolute security of your information, insofar as no information and/or data transmitted via the internet is absolutely secure.  Please note that Lootly data and/or information may be stored on a server in a location or jurisdiction with different laws than the jurisdiction in which you reside, and may be subject to requests and/or access from legal and/or governmental authorities different than those where you reside.</p>
					</div>

					<div class="paragraph">
						<h4><i class="int">VI.</i> European Economic Area (“EEA”) Notice</h4>
						<p>If you are located or otherwise reside in the European Union (hereinafter referred to “EU”), and utilize Lootly products; or if Lootly in any way obtains and/or uses your personal information collected from and/or provided by you through use of our products or otherwise; or if Lootly transfers your personal information in any way, Lootly does so in accordance with this Privacy Policy, the applicable Lootly Terms of Service/Use (incorporated by reference herein), and in compliance with applicable requirements of the General Data Protection Regulation (EU 2016/679) (GDPR).</p>
					</div>

					<div class="paragraph">
						<h4>Transfers of Personal Information </h4>
						<p>Lootly is a data controller and responsible for your Personal Information, which Lootly may process and store in United States of America. The European Commission has decided that United States of America ensures an adequate level of protection of individuals’ Personal Information. Company may use the following safeguards when transferring your personal information to a country, other than United States of America, that is not within the EEA:
						<br>
						(a) Only transfer your Personal Information to countries that have been deemed by the European Commission to provide an adequate level of protection for personal information;
						<br>
						(b) Where Lootly use certain service providers, Lootly may use specific contracts approved by the European Commission which give Personal Information the same protection it has in the EU.
						</p>
					</div>

					<div class="paragraph">
						<h4>Opt-in</h4>
						<p>If you are an EU resident, Lootly may only collect your data using cookies and similar devices, and then track and use your personal information where you have first consented to that. Lootly does not automatically collect personal information from you as described above unless you have consented to us doing so. If you consent to our use of cookies and similar devices, you may at a later date disable them (please see above).</p>
					</div>

					<div class="paragraph">
						<h4>Your Legal Rights</h4>
						<p>Under certain circumstances, you may have rights under the data protection laws in relation to your personal information, including the right to: </p>
						<ul style="list-style: disc;">
							<li>Request access to your personal information.</li>
							<li>Request correction of your personal information.</li>
							<li>Request erasure of your personal information.</li>
							<li>Object to processing of your personal information.</li>
							<li>Request restriction of processing your personal information.</li>
							<li>Request transfer of your personal information.</li>
							<li>Right to withdraw (revoke) consent.</li>
						</ul>
						<p>If you wish to exercise any of these rights, please contact Lootly.</p>
					</div>

					<div class="paragraph">
						<h4>No Fee Usually Required</h4>
						<p>You do not have to pay a fee to access your personal information (or to exercise any of the other rights). However, Lootly my charge a reasonable fee if your request is clearly unfounded, repetitive or excessive. Alternatively, Lootly may refuse to comply with your request in these circumstances.</p>
					</div>

					<div class="paragraph">
						<h4>What Lootly May Need From You</h4>
						<p>Lootly may need to request specific information from you to help us to confirm your identity and ensure your right to access your personal data (or to exercise any of your other rights). This is a security measure to ensure that personal information is not disclosed to any person who has no right to receive it. Lootly may also contact you to ask you for further information in relation to your request to speed up our response.</p>
					</div>

					<div class="paragraph">
						<h4>Time Limit to Respond</h4>
						<p>Lootly tries to respond to all legitimate requests within <b>one (1)</b> month. Occasionally it may take us longer than <b>one (1)</b> month if your request is particularly complex and/or you have made a number of requests. In this case, Lootly will notify you and provide you with updates.</p>
						<p>
							If you have any questions about the Lootly Terms of Service, or if you wish to provide any feedback please contact us: <a href="mailto:support@lootly.io">support@lootly.io</a>
						</p>
						<p>
							Last updated: December 17, 2018
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection