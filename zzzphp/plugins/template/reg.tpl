<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>会员注册-{zzz:sitetitle}</title>
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
                    objtip.text(msg);
                }
            }
        });
    })
</script>
</head>

{zzz:top}
<div class="in_newsbox">
  <div class="register">
    <div class="leftregister">
      <div class="registerbutton">
        <h3>已有账号？</h3>
        <a href="{zzz:sitepath}?location=user&act=login&type=[user:type]&backurl=[user:backurl]" class="nowreg ">立即登陆</a>
        <h3>忘记密码？</h3>
        <a href="{zzz:sitepath}?location=user&act=forget&type=[user:type]&backurl=[user:backurl]" class="nowforget">忘记密码</a> </div>
    </div>
    <div class="rightregister">
      <h2>会员注册</h2>
      <form class="registerform" method="post" action="{zzz:sitepath}form/?reg">
       <input type="hidden" name="type" value="[user:type]">
      <input type="hidden" name="backurl" value="[user:backurl]">
        <div class="formsub">
          <ul>
            <li>
              <label><span class="need">*</span> 账号：</label>
              <input type="text" value="" name="username" class="inputxt" datatype="*5-18" nullmsg="请输入账号 " ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkname " errormsg="账号范围5~18 " />
              <div class="Validform_checktip ">账号范围5~18</div>
            </li>
            <li>
              <label><span class="need ">*</span> 密码：</label>
              <input type="text" value="" name="password" class="inputxt" datatype="*5-16" nullmsg="请输入密码" errormsg="密码范围范围5~16" />
              <div class="Validform_checktip">密码范围范围5~16</div>
            </li>
            {if:{conf:ischeckmobile}=1}
            <li>
              <label><span class="need">*</span> 手机：</label>
              <input type="text" value="" name="mobile" id="phonenum" class="inputxt" datatype="m" nullmsg="请输入手机号 " ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkmobile " errormsg="手机格式不正确 " />
              <div class="Validform_checktip ">手机可作为登陆账号</div>
            </li>
            {end if}
            {if:{conf:ischeckemail}=1}
            <li>
              <label><span class="need">*</span> 邮箱：</label>
              <input type="text" value="" name="email" id="email" class="inputxt" datatype="e" nullmsg="请输入邮箱地址 " ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkemail " errormsg="邮箱格式不正确 " />
              <div class="Validform_checktip "></div>
            </li>
            {end if}
            {if:{conf:usercode}=1}
            <li>
              <label><span class="need ">*</span> 验证码：</label>
              <input type="text " value="" name="code" id="imgcode" class="inputxt" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkcode" datatype="*4-4" nullmsg="请输入验证码" errormsg="验证失败" />
              <div class="imgcode"> <img src="{zzz:sitepath}inc/imgcode.php" id="SeedCode" align="absmiddle" style="cursor:pointer;" border="0"/> </div>
              <div class="Validform_checktip">请输入验证码</div>
            </li>
            {end if}
            {if:{conf:smsmark}=1&&{conf:regsendsms}=1} 
            <script src="{zzz:sitepath}plugins/sms/sms.js" type="text/javascript"></script>
            <li>
              <label><span class="need">*</span>手机验证：</label>
              <input type="text" value="" name="smscode" id="smscode" class="inputxt" datatype="n4-4" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkcode" nullmsg="请输入短信验证码" errormsg="验证码错误">
              <input id="sedcond" value="输入手机号" type="button">
              <div class="Validform_checktip">单击获取短信验证码</div>
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