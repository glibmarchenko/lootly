/*  
| Send users a demo on thier input email
*/

function requestDemo() {
	// document.getElementById('requestDemo').classList.add("overlay-active");
	window.location.href = "/request-demo";
}
window.requestDemo = requestDemo;

function closeOverlay() {
	document.getElementById('requestDemo').classList.remove("overlay-active");
}
window.closeOverlay = closeOverlay;

function submitRequestDemo(){

	$.ajax({
      type: "POST",
      url: "https://lootly.io/demo_submit" ,
      data: {
          name: $("#demo_name").val(),
          email: $("#demo_email").val(),
          website: $("#demo_website").val(),
          title: $("title").text()
      },
      datatype: 'json' ,
      success: (function(res) {
        gtag_report_conversion();
        Intercom('update', {anonymous_email: $("#demo_email").val(), name: $("#demo_name").val(), website: $("#demo_website").val(), utm_source: "Demo Request"});
        document.getElementById('detailsPopup').style.display = "none";
		    document.getElementById('thanksPopup').style.display = "block";
      })
    });
}
window.submitRequestDemo = submitRequestDemo;