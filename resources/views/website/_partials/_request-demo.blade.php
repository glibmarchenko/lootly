<div id="requestDemo" class="request-demo-overlay">
   <div class="container">
      <div class="pop-detail" id="detailsPopup">
         <h2>Interested in <span class="bold">Lootly?</span></h2>
         <p class="white m-b-15">
            Schedule a demo with one of our team members <span>to learn more.</span>
         </p>
         <form id="demoForm">
            <div class="row">
               <div class="col-12">
                  <div class="form-group">
                     <input class="form-control" placeholder="Name" name="demo_name" id="demo_name" type="text">
                  </div>
                  <div class="form-group">
                     <input class="form-control" placeholder="Email" name="demo_email" id="demo_email" type="email">
                  </div>
                  <div class="form-group">
                     <input class="form-control" placeholder="Website" name="demo_website" id="demo_website" type="text">
                  </div>
                  <button type="button" class="btn btn-primary f-s-18 p-b-10 w-100" onclick="submitRequestDemo()">Submit</button>
               </div>
            </div>
         </form>
      </div>
      <div class="thanks-pop-up" id="thanksPopup" style="display: none;">
         <div class="tick-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="148" height="148" viewBox="0 0 72 72">
               <g fill="none" stroke="#1cc286" stroke-width="2">
                  <circle cx="36" cy="36" r="35" style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
                  <path d="M17.417,37.778l9.93,9.909l25.444-25.393" style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
               </g>
            </svg>
         </div>
         <h3>Thank you</h3>
         <p>
            We'll be in touch within
            <br> a few hours to schedule the demo.
         </p>
         <div class="pop-up-btn-wrap">
            <a href="/about" class="btn btn-primary m-r-20">Our Story</a>
         </div>
      </div>
   </div>
   <div class="close-overlay" onclick="closeOverlay()">
      <span></span>
      <span></span>
   </div>
</div>
