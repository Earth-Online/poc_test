<!doctype html public "-//w3c//dtd html 4.01 transitional//en" "http://www.w3c.org/tr/1999/rec-html401-19991224/loose.dtd">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/font-awesome.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/animate.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/style.min.css" rel="stylesheet">
<link href="css/adminstyle.css" rel="stylesheet">
<script src="../js/jquery.min.js"></script>
<script src="../plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<!--[if lte ie 9]>
<script src="../js/respond.min.js"></script>
<script src="../js/html5.js"></script>
<![endif]-->
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">
  <div class="ibox float-e-margins">
    <div class="row m-t">
      <form method="post" action="save.php?act=admingroup" class="form-horizontal" id="contentform">
        <input type="hidden" name="gid" value="[r:gid]"/>
        <div class="col-sm-12  col-md-offset-1">
          <div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5>基本信息</h5>
              <div class="ibox-tools"></div>
            </div>
            <div class="ibox-content">
              <div class="form-group">
                <label class="col-sm-2 control-label  text-danger">组名称</label>
                <div class="col-sm-10">
                  <input type="text" value="[r:g_name]" name="g_name" required id="g_name" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label  text-danger">组权限值</label>
                <div class="col-sm-10">
                  <input type="text" value="[r:g_mark]" required name="g_mark" id="g_mark" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">组描述
                 </label>
                <div class="col-sm-10">
                  <textarea id="g_desc" name="g_desc" class="form-control">{$br_txt [r g_desc]}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12  col-md-offset-1">
          <div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5>功能权限</h5>
              <div class="ibox-tools"><i class="fa fa-info-circle"></i>默认全不选，为包含全部功能，勾选后，只能管理已勾选功能</div>
            </div>
            <div class="ibox-content">
              <div class="form-group">
                <label class="col-sm-1"></label>
                <div class="col-sm-11">{$check_group 'menu',[r g_menu]} </div>
              </div>
            </div>
          </div>
        </div>
       <div class="col-sm-12  col-md-offset-1">
          <div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5>分类权限</h5>
              <div class="ibox-tools"><i class="fa fa-info-circle"></i>默认全不选，为包含全部分类，请确保内容和分类管理开启</div>
            </div>
            <div class="ibox-content">
              <div class="form-group">
                <label class="col-sm-1"></label>
                <div class="col-sm-11"> {$check_group 'sort',[r g_sort]}</div>
              </div>
            </div>
          </div>
        </div>
          <div class="col-sm-12 m-t">
          <div class=" col-sm-10 col-md-offset-1">
            <button class="btn btn-primary" type="submit"><i class="fa fa-floppy-o"></i>　保存内容</button>
            <button class="btn btn-white" onClick="closelayer()" type="reset"><i class="fa fa-close"></i> 返回</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- end panel other --> 
</div>
<script src="../plugins/bootstrap/bootstrap.min.js"></script>
<link href="../plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="../plugins/switchery/switchery.js"></script>
<link href="../plugins/switchery/switchery.css" rel="stylesheet">
<script src="../plugins/layer/layer.min.js"></script> 
<script>
$(function() {
    $(".topmenu").click(function() {  var id=$(this).val(); $(".menu" +id ).prop("checked", $(this).prop("checked")); })
	$(".topsort").click(function() {  var id=$(this).val(); $(".sort" +id ).prop("checked", $(this).prop("checked")); })
}) 
</script> 
<script src="js/adminjs.js"></script>
</body>
</html>