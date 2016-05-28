<?php
if(isset($_POST['blog']))
{
    echo $search = $_POST['blog'];
}
?>
<html>
<head>

<script>
    document.onkeydown=function(evt){
        var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
        if(keyCode == 13)
        {
            document.bloginput.submit();
        }
    }
</script>
</head>
<body>
<form name="bloginput" action="#" method="POST">
<input type="text" name="blog" />.tumblr.com
</form>
</body>
</html>
