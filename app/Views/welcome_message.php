<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to CodeIgniter 4!</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<form action="http://172.20.10.5:8123?" id="form_query" method="post">
    <input type="hidden" name="user" value="default">
    <div class="row">
        <div class="col-12"><textarea name="query" id="" cols="30" rows="10"></textarea></div>
        <div class="col-12"><button class="btn btn-primary">Submit</button></div>
    </div>
</form>
<div id="hasil"></div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-form@4.3.0/dist/jquery.form.min.js"></script>

<script>
$(function(){
    $('#form_query').on('submit',function(ev){
        ev.preventDefault();
        $('#form_query').ajaxSubmit({
            beforeSubmit:function(){
                var xquery = $('#query').val();
                if(xquery=='') {
                    alert('eee');
                } else return true;
                return false
            },
            success:function(retval){
                $('#hasil').text(retval);
            }
        });
        return false;
    });
})
</script>
</body>
</html>