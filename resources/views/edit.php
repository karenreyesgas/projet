<html>
	<body>
		<form id="myForm">
			<label for="modifArticle">Modifier l'article</label>
				<textarea style="display:block;" name="modifArticle" rows="6" placeholder="Je raconte ma vie"></textarea>
			<input type="submit" value="Modifier">
		</form>
    </body>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript">
		var form = $('#myForm');
        form.submit(function (){	
			var data = JSON.stringify(form.serializeArray());
            $.ajax({
                url:window.location.href,
                method:"POST",
                data:data,
                success:function(json){
                    console.log("Success");
					console.log(json);
                },
                error:function(json){
                    console.log("Erreur");
					console.log(json);
                }
            });
			return false;
        });
    </script>
</html>