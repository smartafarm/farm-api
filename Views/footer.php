<?php
?>
</div>	
<div class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
	<div class="container">
		<div class = "navbar-text">
			<p> © <?php echo date('Y').'&nbsp'.PROJECT_NAME?>
		</div>
		
	</div> 
</div>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <?php
		if (isset($this->js)) 
		{
			foreach ($this->js as $js)
			{
				echo '<script type="text/javascript" src="'.URL.'views/'.$js.'"></script>';
			}
		}
	?>
</body>
</html>