<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- standalone contact.php V4.00 - 24-Feb-2021 -->
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Contact</title>
<style type="text/css">
body {
  color: black;
  background-color: #F3F2EB;
  font-family: verdana, helvetica, arial, sans-serif;
  font-size: 73%;  /* Enables font size scaling in MSIE */
  margin: 0;
  padding: 0;
}

html > body {
  font-size: 9pt;
}

#page {
        margin: 20px auto;
        color: black;
        background-color: white;
        padding: 0;
        width: 800px;
        border: 1px solid #959596;
}
#main-copy {
  color: black;
  background-color: white;
  text-align: left;
  line-height: 1.5em;
  margin: 0 2em;
  padding: .5ex 1em 1em 1em;
}


#main-copy h1 {
  color: black;
  background-color: transparent;
  font-family: arial, verdana, helvetica, sans-serif;
  font-size: 175%;
  font-weight: bold;
  margin: 1em 0 0 0;
  padding: 1em 0 0 0;
}

#main-copy a {
  color: #336699;
  background-color: transparent;
  text-decoration: underline;
}
#main-copy a:hover {
  text-decoration: none;
}
p {
  margin: 1em 0 1.5em 0;
  padding: 0;
}
.warningBox {
  color: white;
  font-size: 13px;
  text-align: center;
  background-color: #CC0000;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 1px dashed rgb(255,255,255);
}

.input p { font-family:arial; font-size:1em }
.input a { text-decoration:none }
.input { width:140px; margin-left:50px; padding:10px; background-color:#B0FFB0; border:1px solid grey }
.db { width:33px; height:33px }
.db a:hover { color:red !important; }
.challenge {
 font-size: x-large;
 margin-left: 50px;
 border: 2px blue solid;
 padding: 10px;
}
.input input[type="button"] {
  border-radius: 10px !important;
  font-size: x-large !important;
  width: 33px !important;
  height: 33px !important;
  border: 1px solid black !important;
  color: black !important;
  margin: 1px;
}
.input input[type="button"]:hover {
  color: red !important;
}
.input input[type="submit"] {
  border: 1px solid black !important;
  color: black !important;
}
.input input[type="submit"]:hover {
  color: red !important;
}

.input input[type="reset"] {
  border: 1px solid black !important;
  color: black !important;
}
.input input[type="reset"]:hover {
  color: red !important;
}

.input input[type="password"] {
  border: 1px solid black !important;
  color: black !important;
}
</style>
</head>

<body>
<div id="page">
  <div id="main-copy">
<?php 
  $doStandalone = true;
	include_once("contactLP-inc.php");
?>
  </div> <!-- end div main-copy -->
</div> <!-- end div page -->
</body>
</html>