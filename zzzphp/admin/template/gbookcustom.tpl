<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/font-awesome.min.css" rel="stylesheet">
<link href="../plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/animate.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/style.min.css" rel="stylesheet">
<link href="css/adminstyle.css" rel="stylesheet">
<script src="../js/jquery.min.js"></script>
<script>var table='content_custom';</script>
<!--[if lte IE 9]>
<script src="../plugins/bootstrap/respond.min.js"></script>
<script src="../plugins/bootstrap/html5.js"></script>
<![endif]-->
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">
  <div class="ibox float-e-margins">
    <div class="ibox-content">
      <div class="row row-lg">
        <div class="col-sm-12">
          <div class="col-sm-5">
            <button id="add" class="btn  btn-primary" type="button" onclick="gbookcustom('')"><i class="fa fa-plus">　</i>添加参数</button>
            <button onClick="location.reload()" class="btn" type="button"><i class="fa fa-refresh"></i> 刷新</button>
            <button onClick="location.href='?gbooklist'" class="btn" type="button"><i class="fa fa-reply">　</i>返回</button>
          </div>
          <table id="table"
           data-toggle="table"
           data-pagination="true"
           data-show-toggle="true"
           data-show-columns="true"
           data-id-field="id"       
           data-id-table="advancedTable"
           data-page-size="20"
           data-page-list="[20,50,100]" >
            <thead>
              <tr>
                <th class="tableid" data-field="id" data-sortable="true">ID</th>
                <th class="tablename" data-field="Customname"  data-sortable="true">参数名称</th>
                <th class="tabletype" data-field="Custom"  data-sortable="true">参数</th>
                <th class="tablename" data-field="customOptions"  data-sortable="true">备选内容</th>
                <th class="tablename" data-field="customValues"  data-sortable="true">默认值</th>
                <th class="tablename" data-field="customplace"  data-sortable="true">规则</th>
                <th class="tableorder" data-field="CustomOrder"  data-sortable="true">排序</th>
                <th class="tableonoff" data-field="onoff" data-sortable="true">开关</th>
              </tr>
            </thead>                      
            <?php			
	$data=db_load("content_custom","customtype='gbook'");
	foreach ($data as $value){
    	$value=array_change_key_case($value);
    	$i=$value['customid'];
		echo "<tr>
		<td>".$i."</td>	
		<td><input type='text'  id='customname-".$value['customname']."' placeholder='".($value['customname'])."' value='".($value['customname'])."'></td>	
		<td>".$value['custom']."</td>
		<td><input type='text' class='title' id='customoptions-".$i."' placeholder='".($value['customoptions'])."' value='".($value['customoptions'])."'></td>	
		<td><input type='text' class='title' id='customvalues-".$i."' placeholder='".($value['customvalues'])."'  value='".($value['customvalues'])."'></td>
		<td><select name='customplace'  id='customplace-".$i."' class='form-control'>".select_test(($value['customplace']))."</select></td>
		<td><input type='number' class='order' id='customorder-".$i."' placeholder='".$value['customorder']."'  value='".$value['customorder']."'></td>		
		<td><button type='button'  onclick=delcustom('".$value['custom']."')  class='btn btn-danger dim'><i class='fa fa-times'></i></button></td>
		</tr>";
	};
		?>
          </table>
          </FORM>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="gbookcustom" style="display:none">
<form method="post" action="save.php?act=custom" class="form-horizontal" id="contentform">
<input name="customtype[]" value="gbook" type="hidden">
  <div class="col-sm-12">
    <div class="ibox-content">
      <div class="form-group">
        <label class="col-sm-2 control-label">参数名称</label>
        <div class="col-sm-4">
          <input type="text" name="customname" required id="CustomName" value="" class="form-control">
        </div>
        <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 必填，参数的中文名称，如价格</span> </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">字段名</label>
        <div class="col-sm-4">
          <div class="input-group"> <span class="input-group-addon">z</span>
            <input type="text" name="custom" required id="Custom" value="" class="form-control">
          </div>
        </div>
        <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 必填，即为字段名,　如price </span> </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">参数类型</label>
        <div class="col-sm-4">
          <select name="customclass" class="form-control customclass">
            <option value="11">文本</option>
            <option value="2">文本域</option>
            <option value="4">单选</option>
            <option value="5">多选</option>
            <option value="6">下拉</option>
          </select>
        </div>
      </div>
      <div class="form-group numtype" style="display:none">
        <label class="col-sm-2 control-label">数字类型</label>
        <div class='col-sm-4'>
          <label>
            <input type="radio" name="numtype" value="int" id="numtype_0" checked>
            长整数</label>
          <label>
            <input type="radio" name="numtype" value="money" id="numtype_2">
            货币</label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">备选内容</label>
        <div class="col-sm-4">
          <input type="text" name="customoptions" id="customOptions" value="" class="form-control">
        </div>
        <div class="help-block m-b-none"><i class="fa fa-info-circle"></i> <span id="options">单选和多选必须有备选，用逗号分割</span></div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">默认内容</label>
        <div class="col-sm-4">
          <input type="text" name="customvalues" id="customValues" value="" class="form-control">
        </div>
        <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 数字类型默认内容必须有默认值</span> </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">必填规则</label>
        <div class="col-sm-4">
          <select name='customplace'  id='customplace' class='form-control'>{$select_test ""} </select>
        </div>
      </div>
    </div>
    <div class="col-sm-12 m-t">
      <div class=" col-sm-10 col-md-offset-1">
        <button class="btn btn-primary" type="submit"> <i class="fa fa-floppy-o"></i>　保存内容 </button>
        <button class="btn btn-white" data-dismiss="modal"> <i class="fa fa-close"></i> 返回 </button>
      </div>
    </div>
  </div>
  </div>
</form>
<!-- End Panel Other -->
</div>

<!-- End Panel Other --> 
<script src="../plugins/bootstrap/bootstrap.min.js"></script> 
<script src="../plugins/bootstrap-table/bootstrap-table.min.js"></script> 
<script src="../plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script> 
<script src="../plugins/layer/layer.min.js"></script> 
<script src="js/adminjs.js"></script> 
<script>
function gbookcustom(){
	layer.open({type: 1,title:"添加参数", area: ['800px', '500px'],content:$("#gbookcustom")})
}
</script>
</body>
</html>
