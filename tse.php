<input type="hidden" name="token" id="token" value="" />
<input type="hidden" name="error_token" id="error_token" value="0" />
<?php

	if($nav=="Chrome" or $nav=="Other")
	{
		/*
	?>
	<script>
	$( document ).ready(function() {
		 window.location.href= "mobincube://javascript/getVariables('{fcmToken}')";
	
		
	  });
	
	function getVariables(fcmToken){
			
		   document.getElementById('token').value=fcmToken;
	
	  }
	

 </script>
	<?php
	*/
	
	
	?>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js?v=<?php echo(rand()); ?>"></script>
	<script>
	
	 //if (matchMedia && window.matchMedia('(min-device-width: 320px) and (max-device-width: 480px)').matches)
	 //{

		  //FIREBASE
		  ios_token=setupWKWebViewJavascriptBridge(function(bridge) {
			
							 bridge.init();
							
							 })
							
		
		  if(ios_token==undefined)
		  {
			  document.getElementById("error_token").value="1";
			
			  function getVariables(fcmToken){
			
			   document.getElementById('token').value=fcmToken;
		
		  }
		
			  $( document ).ready(function() {
			
			window.location.href= "mobincube://javascript/getVariables('{fcmToken}')";
			
			});
		  }
	 //}
	  </script>


	<?php
	
	  }
	?>