function checkcity(id,type){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_city",{id:id,type:type},function(data){
			if(type==1){
				$("#cityid").html(data);
			}else{
				$("#three_cityid").html(data);
			}
		})
	}else{
		if(type==1){
			$("#cityid").html('<option value="">请选择</option>');
		}
		$("#three_cityid").html('<option value="">请选择</option>');
	}
}
function checkinfo(){
	var name=$.trim($("input[name='name']").val());
	var birthday=$.trim($("input[name='birthday']").val());
	var living=$.trim($("input[name='living']").val());
	var email=$.trim($("input[name='email']").val());
	var telphone=$.trim($("input[name='telphone']").val());
	var description=$.trim($("#description").val());
	ifemail = check_email(email);  
	telphone = isjsMobile(telphone);  
	if(name==""){alert('请填写姓名！');return false;}
	if(birthday==""){alert('请填写出生年月！');return false;}
	if(telphone==false){alert('请正确填写手机号码！');return false;}
	if(ifemail==false){alert('请填写电子邮件！');return false;}
	if(living==""){alert('请填写现居住地！');return false;}
	if(description==""){alert("请填写自我评价！");return false;}
}
function check_email(strEmail) {
	 var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/;
	 if (emailReg.test(strEmail))
	 return true;
	 else
	 return false;
 }
function isjsMobile(obj){
	if(obj.length!=11) return false;
	else if(obj.substring(0,2)!="13" && obj.substring(0,2)!="15" && obj.substring(0,2)!="18") return false;
	else if(isNaN(obj)) return false;
	else  return true;
}
function checkshow(id){
	if(id=="expect"){
		$("#infobutton").show();
		$("#info").hide();
	}else if(id=="info"){
		$("#expectbutton").show();
		$("#expect").hide();
	}
	$("#"+id+"button").hide();
	$("#"+id).show();
}function checkhide(id){
	$("#"+id+"button").show();
	$("#"+id).hide();
}
function saveexpect(){
	var name=$.trim($("#expect_name").val());
	var hy=$.trim($("#hy").val());
	var job_classid=$.trim($("#job_classid").val());
	var provinceid=$.trim($("#provinceid").val());
	var cityid=$.trim($("#cityid").val());
	var three_cityid=$.trim($("#three_cityid").val());
	var salary=$.trim($("#salary").val());
	var report=$.trim($("#report").val());
	var eid=$.trim($("#eid").val());
	if(name==""){
		alert('请填写简历名称！');return false;
	}
	if(job_classid==""){
		alert('请选择期望从事职位！');return false;
	}
	if(cityid==""){
		alert('请选择期望工作地点！');return false;
	}
	$.post(weburl+"/wap/member/index.php?c=expect",{name:name,hy:hy,job_classid:job_classid,provinceid:provinceid,cityid:cityid,three_cityid:three_cityid,salary:salary,report:report,eid:eid},function(data){
		if(data>0){
			alert('保存成功！');
			window.location.href='index.php?c=addresume&eid='+data;
		}else{
			alert('保存失败！');
		}
	})
}

function checkskill(){
	var name=$.trim($("input[name='name']").val());
	var longtime=$.trim($("input[name='longtime']").val());
	if(name==""){
		alert('请填写技能名称！');return false;
	}
	if(longtime==""){
		alert('请填写掌握时间！');return false;
	}
}
function checkwork(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("input[name='syear']").val());
	var smouth=$.trim($("input[name='smouth']").val());
	var sday=$.trim($("input[name='sday']").val());
	var eyear=$.trim($("input[name='eyear']").val());
	var emouth=$.trim($("input[name='emouth']").val());
	var eday=$.trim($("input[name='eday']").val());
	var department=$.trim($("input[name='department']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		alert('请填写单位名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		alert('请正确填写工作时间！');return false;
	}
	if(department==""){
		alert('请填写所在部门！');return false;
	}
	if(title==""){
		alert('请填写担任职位！');return false;
	}
	if(content==""){
		alert('请填写工作内容！');return false;
	}
}
function checkproject(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("input[name='syear']").val());
	var smouth=$.trim($("input[name='smouth']").val());
	var sday=$.trim($("input[name='sday']").val());
	var eyear=$.trim($("input[name='eyear']").val());
	var emouth=$.trim($("input[name='emouth']").val());
	var eday=$.trim($("input[name='eday']").val());
	var sys=$.trim($("input[name='sys']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		alert('请填写项目名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		alert('请正确填写项目时间！');return false;
	}
	if(sys==""){
		alert('请填写项目环境！');return false;
	}
	if(title==""){
		alert('请填写担任职位！');return false;
	}
	if(content==""){
		alert('请填写项目内容！');return false;
	}
}
function checkedu(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("input[name='syear']").val());
	var smouth=$.trim($("input[name='smouth']").val());
	var sday=$.trim($("input[name='sday']").val());
	var eyear=$.trim($("input[name='eyear']").val());
	var emouth=$.trim($("input[name='emouth']").val());
	var eday=$.trim($("input[name='eday']").val());
	var specialty=$.trim($("input[name='specialty']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		alert('请填写学校名称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		alert('请正确填写在校时间！');return false;
	}
	if(specialty==""){
		alert('请填写所学专业！');return false;
	}
	if(title==""){
		alert('请填写担任职位！');return false;
	}
	if(content==""){
		alert('请填写专业描述！');return false;
	}
}
function checktraining(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("input[name='syear']").val());
	var smouth=$.trim($("input[name='smouth']").val());
	var sday=$.trim($("input[name='sday']").val());
	var eyear=$.trim($("input[name='eyear']").val());
	var emouth=$.trim($("input[name='emouth']").val());
	var eday=$.trim($("input[name='eday']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		alert('请填写培训中心！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		alert('请正确填写培训时间！');return false;
	}
	if(title==""){
		alert('请填写培训方向！');return false;
	}
	if(content==""){
		alert('请填写培训描述！');return false;
	}
}
function checkcert(){
	var name=$.trim($("input[name='name']").val());
	var syear=$.trim($("input[name='syear']").val());
	var smouth=$.trim($("input[name='smouth']").val());
	var sday=$.trim($("input[name='sday']").val());
	var eyear=$.trim($("input[name='eyear']").val());
	var emouth=$.trim($("input[name='emouth']").val());
	var eday=$.trim($("input[name='eday']").val());
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(name==""){
		alert('请填写证书全称！');return false;
	}
	if(syear==""||smouth==""||sday==""||eyear==""||emouth==""||eday==""){
		alert('请正确填写有效时间！');return false;
	}
	if(title==""){
		alert('请填写颁发单位！');return false;
	}
	if(content==""){
		alert('请填写证书描述！');return false;
	}
}
function checkother(){
	var title=$.trim($("input[name='title']").val());
	var content=$.trim($("textarea[name='content']").val());
	if(title==""){
		alert('请填写其他标题！');return false;
	}
	if(content==""){
		alert('请填写其他描述！');return false;
	}
}
function checkjob1(id){
	var style=$("#joblist"+id).attr("style");
	$(".onelist").addClass("lookshow");
	$(".lookhide").attr("style","display: none;");
	if(style=="display: none;"){
		$("#joblist"+id).show();
		$("#job"+id).removeClass("lookshow");
	}
}
function checkjob2(id){
	var style=$("#jobpost"+id).attr("style");
	$(".post_show_three").attr("style","display: none;");
	if(style=="display: none;"){
		$("#jobpost"+id).show();
	}
}
function realy(){
	var info="";
	var value="";
	$("input[name='jobclass']:checked").each(function(){
		var obj = $(this).val();
		var name = $(this).attr("data");
		if(info==""){
			info=obj;
			value=name;
		}else{
			info=info+","+obj;
			value=value+","+name;
		}
	})
	if(info==""){
		alert('请选择职位类别！');return false;
	}else{
		$("#job_classid").val(info);
		$("#jobclassbutton").val(value);
		Close();
	}
}
function checkshowjob(){
	$("#joblist").show();
	checkhide('info');
	$("#bg").show();
}
function checked_input(id){
	var check_length = $("input[name='jobclass']:checked").length;
	if(check_length>5){ 
		alert('您最多只能选择五个！'); 
		$("#r"+id).attr("checked",false);
	}
}
function Close(){
	$("#joblist").hide();
	
}