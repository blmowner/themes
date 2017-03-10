/*
windowopen.js
V 1.0
copyright Andrew Holt 
This script is free to use provided this notice remains.
Full instructions can be found @ http://www.webdevtips.com/webdevtips/js/openwindow.shtml
*/


function open_win(what_link,the_x,the_y,toolbar,addressbar,directories,statusbar,menubar,scrollbar,resize,history,pos,wname){ 
var the_url = what_link;
the_x -= 0;
the_y -= 0;
var how_wide = screen.availWidth;
var how_high = screen.availHeight;
if(toolbar == "0"){var the_toolbar = "no";}else{var the_toolbar = "yes";}
if(addressbar == "0"){var the_addressbar = "no";}else{var the_addressbar = "yes";}
if(directories == "0"){var the_directories = "no";}else{var the_directories = "yes";}
if(statusbar == "0"){var the_statusbar = "no";}else{var the_statusbar = "yes";}
if(menubar == "0"){var the_menubar = "no";}else{var the_menubar = "yes";}
if(scrollbar == "0"){var the_scrollbars = "no";}else{var the_scrollbars = "yes";}
if(resize == "0"){var the_do_resize =  "no";}else{var the_do_resize = "yes";}
if(history == "0"){var the_copy_history = "no";}else{var the_copy_history = "yes";}
if(pos == 1){top_pos=0;left_pos=0;}
if(pos == 2){top_pos = 0;left_pos = (how_wide/2) -  (the_x/2);}
if(pos == 3){top_pos = 0;left_pos = how_wide - the_x;}
if(pos == 4){top_pos = (how_high/2) -  (the_y/2);left_pos = 0;}
if(pos == 5){top_pos = (how_high/2) -  (the_y/2);left_pos = (how_wide/2) -  (the_x/2);}
if(pos == 6){top_pos = (how_high/2) -  (the_y/2);left_pos = how_wide - the_x;}
if(pos == 7){top_pos = how_high - the_y;left_pos = 0;}
if(pos == 8){top_pos = how_high - the_y;left_pos = (how_wide/2) -  (the_x/2);}
if(pos == 9){top_pos = how_high - the_y;left_pos = how_wide - the_x;}
if (window.outerWidth ){
var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",outerWidth="+the_x+",outerHeight="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
wname=window.open(the_url, wname, option);
wname.focus();
}
else
{
var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",Width="+the_x+",Height="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
if (!wname.closed && wname.location){
wname.location.href=the_url;
}
else
{
wname=window.open(the_url, wname, option);
//wname.resizeTo(the_x,the_y);
wname.focus();
wname.location.href=the_url;
}
}
} 