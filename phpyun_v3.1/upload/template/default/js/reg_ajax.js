
$(function(){
	$(".tips_class").blur(function(){
		var name=$(this).attr('name');
		reg_checkAjax(name);
	});
});
function reg_checkAjax(id){
	var obj = $.trim($("#"+id).val());
	var msg;
	if(id=="username"){
		if(obj==""){
			msg='用户名不能为空！';
			return update_html(id,"0",msg);
		}else if(obj.length<2||obj.length>16){
			msg='请输入2至16位用户名！';
			return update_html(id,"0",msg);
			return false;
		}else{
			$.post("index.php?m=register&c=ajax_reg",{username:obj},function(data){
				if(data==0){
					msg='填写正确！';
					return update_html(id,"1",msg);
				}else{
					msg="用户名已存在！";
					return update_html(id,"0",msg);
					return false;
				}
			});
		}
	}

	if(id=="password"){
		if(obj==""){
			 msg='密码不能为空！';
			 return update_html(id,"0",msg);
		 }else if(obj.length<6 || obj.length>20 ){
			 msg='只能输入6至20位密码！';
			 return update_html(id,"0",msg);
		 } else{
			 msg='输入成功！';
			 return update_html(id,"1",msg);
		 }
	}
	if(id=="passconfirm")
	{
		var obj2 = $("#password").val();
		 if(obj==""){
			 msg="请确认密码！";
			 return update_html(id,"0",msg);
		 }else if(obj2!=obj){
			msg="两次输入密码不同！";
			 return update_html(id,"0",msg);
		}else{
			msg="输入正确！";
			return update_html(id,"1",msg);
		 }

	}
	if(id=="moblie"){
		var reg= /^[1][3458]\d{9}$/;
		if(obj==''){
			msg="手机号不能为空！";
			 return update_html(id,"0",msg);
		}else if(!reg.test(obj)){
			msg="手机号码格式错误！";
			 return update_html(id,"0",msg);
		 }else{
			$.post("index.php?m=register&c=regmoblie",{moblie:obj},function(data){
				if(data==0){
					msg='填写正确！';
					return update_html(id,"1",msg);
				}else{
					msg="号码已存在！";
					return update_html(id,"0",msg);
					return false;
				}
			});
		 }

	}
	if(id=="address"){
		 if(obj==""){
			msg="地址不能为空！";
			 return update_html(id,"0",msg);
		 }else{
			msg="输入成功！";
			return update_html(id,"1",msg);
		 }
	}
	if(id=="unit_name"){
		 if(obj==""){
			msg="公司名称不能为空！";
			return update_html(id,"0",msg);
		 }else{
			msg="输入成功！";
			return update_html(id,"1",msg);
		 }
	}
	if(id=="email"){
	   var myreg = /^([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9\-]+@([a-zA-Z0-9\-]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	   if(!myreg.test(obj)){
			msg="邮箱格式错误！";
			return update_html(id,"0",msg);
	   }else{
		$.post("index.php?m=register&c=ajax_reg",{email:obj},function(data){
			if(data==0){
				msg="输入成功！";
				return update_html(id,"1",msg);
			}else{
				msg="邮箱已被使用！";
				return update_html(id,"0",msg);
			}
		});
	   }
	}
}
function update_html(id,type,msg){
	$("#check_"+id).val(type);
	$("#ajax_"+id).html(msg);
	if(type=="0"){
		$("#"+id).attr('date','0');
		layer.tips(msg, "#"+id, {guide: 1,style: ['background-color:#F26C4F; color:#fff;top:1px', '#F26C4F']});
	}else{
		$("#"+id).attr('date','1');
		$("#"+id).addClass('logoin_text_success');
	}
}
function check_user(){
	var loadi = layer.load('正在注册……',0);
	var username=$.trim($("#username").val());
	var password=$.trim($("#password").val());
	var passconfirm=$.trim($("#passconfirm").val());
	var email=$.trim($("#email").val());
	var moblie=$.trim($("#moblie").val());
	var authcode=$.trim($("#txt_CheckCode").val());
	if($("#username").attr('date')!="1"||$("#password").attr('date')!="1"||$("#email").attr('date')!="1"||$("#moblie").attr('date')!="1"||$("#passconfirm").attr('date')!="1"){
		var vid=$("input[date!='1']").attr('id');
		layer.msg('请正确输入以上信息！', 2, 8,function(){if(vid){var msg=$("#ajax_"+vid).html();layer.tips(msg, "#"+vid, {guide: 1,style: ['background-color:#F26C4F; color:#fff;top:1px', '#F26C4F']});}});
	}else{
		if($("#xieyi").attr("checked")!='checked'){
			layer.msg('您必须同意注册协议才能成为本站会员！', 2, 8);return false;
		}else{
			$.post("index.php?m=register&c=regsave",{username:username,passconfirm:passconfirm,password:password,email:email,moblie:moblie,authcode:authcode,usertype:1},function(msg){
				layer.close(loadi);
				if(msg==1){
					window.location.href=weburl+"/member/index.php?c=info";//注册成功
				}else{
					var date=msg.split("##");
					layer.msg(date[1], 2, date[0]);return false;
				}
			});
		}
	}
}
function check_com(){
	var username=$.trim($("#username").val());
	var password=$.trim($("#password").val());
	var passconfirm=$.trim($("#passconfirm").val());
	var email=$.trim($("#email").val());
	var moblie=$.trim($("#moblie").val());
	var address=$.trim($("#address").val());
	var unit_name=$.trim($("#unit_name").val());
	var authcode=$.trim($("#txt_CheckCode").val());
	var loadi = layer.load('正在注册……',0);
	if($("#username").attr('date')!="1"||$("#password").attr('date')!="1"||$("#passconfirm").attr('date')!="1"||$("#email").attr('date')!="1"||$("#moblie").attr('date')!="1"||$("#address").attr('date')!="1"||$("#unit_name").attr('date')!="1"){
		var vid=$("input[date!='1']").attr('id');
		layer.msg('请正确输入以上信息！', 2, 8,function(){if(vid){var msg=$("#ajax_"+vid).html();layer.tips(msg, "#"+vid, {guide: 1,style: ['background-color:#F26C4F; color:#fff;top:1px', '#F26C4F']});}});
	}else{
		if($("#xieyi").attr("checked")!='checked'){
			layer.msg('您必须同意注册协议才能成为本站会员！', 2, 8);return false;
		}else{
			$.post("index.php?m=register&c=regsave",{username:username,passconfirm:passconfirm,password:password,email:email,moblie:moblie,address:address,unit_name:unit_name,authcode:authcode,usertype:2},function(msg){
				layer.close(loadi);
				if(msg==1){
					window.location.href=weburl+"/member/index.php";//注册成功
				}else{
					var data=msg.split('##');
					if(data[0]==8){
						layer.msg(data[1], 2, 8);
					}else if(data[0]==7){
						layer.msg(data[1], 2, 9,function(){location.href =weburl;});
					}
				}
			});
		}
	}
}
function checklogin(){
	if($.trim($("#loginname").val())==""||$.trim($("#loginpassword").val())==""){
		$("#error_login").html("<font color='red'>用户名或密码不能为空！</font>");return false;
	}else{return true;}
}
//--------------------------------------------------------------------------------------
function check_login(){
	var username=$("#username").val();
	var password=$("#password").val();
	var usertype=$("#usertype").val();
	if($("input[name=loginname]").attr("checked")=='checked'){
		var loginname=1;
	}else{
		var loginname=0;
	}
	var path=$("#path").val();
	if(username=="" || username=="用户名"){
		$("#show_name").show();
		$("#username").focus(
		    function(){
		       $("#show_name").hide();
		    }
		);
		return false;
	}else{
	    $("#show_name").hide();
	}
	if(password==""){
		$("#show_pass").show();
		$("#password").focus(
		    function(){
			    $("#show_pass").hide();
			}
		);
		return false;
	}else{
	    $("#show_pass").hide();
	}
	var authcode=$("#txt_CheckCode").val();
	$.post("index.php?m=login&c=loginsave",{username:username,password:password,usertype:usertype,path:path,loginname:loginname,authcode:authcode},function(data){
		if(data.indexOf('script')>0 || data==1)
		{
			if(data.indexOf('script'))
			{
				layer.load('登陆中,请稍候...');
				$('#uclogin').html(data);
				setTimeout("window.location.href='"+weburl+"/member';",500);
			}else{
				window.location.href=weburl+"/member";
			}
		}else if(data==2){

			window.location.href=weburl+"/member/index.php?c=info";

		}else{
			layer.msg(data, 2, 8);
			if(data=="您的账户还未激活，请先激活!"){
				window.location.href=weburl+"/index.php?m=activate";
			}
		}
	})
}
function checktype(id){
	$(".log_cur").attr("class","");
	$("#usertype"+id).attr("class","log_cur");
	$("#usertype").val(id);
	var url=$("#regurl").attr("href");
	if(id==1){
		url=url.replace("2",1);
	}else{
		url=url.replace("1",2);
	}
	$("#regurl").attr("href",url);
}