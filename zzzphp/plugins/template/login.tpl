<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>会员登录-{zzz:sitetitle}</title>
<meta name="description" content="{zzz:sietdesc}"/>
<meta name="Keywords" content="{zzz:sitekeys}"/>
<meta name="author" content="http://www.zzzcms.com"/>
<link rel="stylesheet" type="text/css" href="{zzz:tempath}Css/styles.css"/>
<script src="{zzz:sitepath}js/jquery.min.js" type="text/javascript"></script>
<script src="{zzz:plugpath}layer/layer.min.js" type="text/javascript"></script>
<script src="{zzz:sitepath}js/zzzcms.js" type="text/javascript"></script>
<link href="{zzz:sitepath}plugins/Validform/Validform.css" rel="stylesheet" />
<link href="{zzz:plugpath}template/user.css" rel="stylesheet" />
<script src="{zzz:sitepath}plugins/Validform/Validform.min.js"></script>
<script type="text/javascript">
    $(function() {
        $(".registerform").Validform({
            tiptype: function(msg, o, cssctl) {
                if (!o.obj.is("form")) {
                    var objtip = o.obj.siblings(".Validform_checktip");
                    cssctl(objtip, o.type);
					console.log(o.type);
                    objtip.text(msg);
					
                }
            }
        });   
 })
 function wxlogin(){
	layer.open({
	type: 2,
	shadeClose: true,
	 anim: 1,
	area: ['600px', '550px'],
	title: false,
	content: "../plug/wxlogin/"
	})
}
</script>
</head>
{zzz:top}
<div class="in_newsbox">
  <div class="register">
    <div class="leftregister">
      <div class="registerbutton">
        <h3>没有账号？</h3>
        <a href="{zzz:sitepath}?location=user&act=reg&type=[user:type]&backurl=[user:backurl]" class="nowreg">注册账号</a>
        <h3>忘记密码？</h3>
        <a a href="{zzz:sitepath}?location=user&act=forget&type=[user:type]&backurl=[user:backurl]" class="nowforget">忘记密码</a> </div>
    </div>
    <div class="rightregister">
      <h2>会员登陆</h2>
      <form class="registerform" method="post" action="{zzz:sitepath}form/?login">
      <input type="hidden" name="type" value="[user:type]">
      <input type="hidden" name="backurl" value="[user:backurl]">
        <div class="formsub">
          <ul>
            <li>
              <label><span class="need">*</span> 账号：</label>
              <input type="text" value="" name="username" placeholder="账号/手机号" class="inputxt" datatype="*5-18" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkname&checktype=1" nullmsg="请输入账号" errormsg="账号范围5~18" />
              <div class="Validform_checktip">账号范围5~18</div>
            </li>
            <li>
              <label><span class="need">*</span> 密码：</label>
              <input type="password" value="" name="password" class="inputxt" datatype="*5-16" nullmsg="请输入密码" errormsg="密码范围范围5~16" />
              <div class="Validform_checktip">密码范围5~16</div>
            </li>
           {if:{conf:usercode}=1}
            <li>
            <label><span class="need">*</span> 验证码：</label>
            <input type="text" value="" name="code" id="imgcode" class="inputxt" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkcode" datatype="*4-4" nullmsg="请输入验证码" errormsg="验证码错误" />
              <div class="imgcode"><img src="{zzz:sitepath}inc/imgcode.php" id="SeedCode" align="absmiddle" style="cursor:pointer;" border="0" onClick="changeCode()"/> </div>
              <div class="Validform_checktip">请输入验证码</div>
            </li>
           {end if}          
          </ul>
          <div class="action">
            <input type="submit" value="提交" />
            <input type="reset" value="重置" />
          </div>
        </div>
      </form>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
{zzz:foot}
</body></html>