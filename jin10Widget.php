<?php
/*
Plugin Name: 金十数据
Plugin URI: http://233.imjs.work/2369.html
Description: 在您网站上显示金十数据的最新新闻与报价，安装完成后请到外观-->小工具页添加小工具。
Version: 0.0.7
Author: JohnShen
Author URI: http://233.imjs.work
License: GPL
*/
/*  Copyright 2015  JohnShen  (email : jshensh@126.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
add_action( 'widgets_init', 'jin10LoadWidgets' );

function jin10LoadWidgets() {
    register_widget( 'jin10Widget' );
}

class jin10Widget extends WP_Widget {

    function jin10Widget() {
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'jin10', 'description' => '金十数据上的消息与报价' );

        /* Widget control settings. */
        $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'jin10-widget' );

        /* Create the widget. */
        $this->WP_Widget( 'jin10-widget', '金十数据', $widget_ops, $control_ops );
    }

    function widget( $args, $instance ) {
        extract( $args );

        $title = apply_filters('widget_title', $instance['title'] );
        $server = $instance['server'];
        $variety = $instance['variety'];

        echo $before_widget;

        if ( $title )
            echo $before_title . $title . $after_title;

?>
<audio id="ring" src="http://cdn.jin10x.com/images/notice.wav"></audio>

<span id="variety"></span>

<form>
<p style="font-size: 12px !important; margin: 0;"><label style="vertical-align:middle;"><input type="checkbox" id="economicsPlaySound" style="vertical-align:middle;">播放提示音</input></label>&nbsp;<label style="vertical-align:middle;"><input type="checkbox" id="economicsPlaySoundImportant" style="vertical-align:middle;">仅在有重要消息时提醒我</input></label></p>
</form>
<script type="text/javascript" src="http://<?php echo $server; ?>/socket.io/socket.io.js"></script>
<script src="<?php echo plugins_url('jquery.cookie.js',__FILE__) ; ?>"></script>
<script type="text/javascript">
    jQuery(function() {
        if (jQuery.cookie("economics")>0) {
            jQuery("#economicsPlaySound").attr("checked","checked");
            if (jQuery.cookie("economics")==2) {
                jQuery("#economicsPlaySoundImportant").attr("checked","checked");
            }
        }
        jQuery("#economicsPlaySound").click(function() {
            if (!this.checked) {
                jQuery.cookie("economics",0,{ expires: 30 });
                document.getElementById("economicsPlaySoundImportant").checked=false;
            } else {
                jQuery.cookie("economics",1,{ expires: 30 });
            }
        });
        jQuery("#economicsPlaySoundImportant").click(function() {
            if (this.checked) {
                jQuery.cookie("economics",2,{ expires: 30 });
                document.getElementById("economicsPlaySound").checked=true;
            } else {
                jQuery.cookie("economics",1,{ expires: 30 });
            }
        });
    });
</script>
<ul id="listarea">
</ul>
<style>
#listarea {
    max-height: 350px;
    overflow: auto;
}
#listarea li {
    display: block;
    line-height: 20px;
    margin: 2px 0 10px 0 !important;
    min-height: 20px;
}
#listarea li p {
    margin: 0;
    font-size: 12px !important;
}
#listarea li img {
    padding-bottom: 0 !important;
}
</style>

<script type="text/javascript">
    var template1 = "<!-- {pic} {have} --><li{color}><small>{time}</small>{more}<p style=\"padding: 0 !important;\">{content}</p></li>";
        
    var template2 = "<!-- {pic} --><li{color}><small>{time}</small><p style=\"padding: 0 !important;\">{mingcheng}</p>前值：{qianzhi} 预期：{yuqi}，公布：{gongbu}</p><p style=\"padding: 0 !important;\">{xingji} 星数据，影响{auagoil}：{yingxiang}</p></li>";

    var jin10VarietyArr={"XAUUSD":"现货黄金","XAGUSD":"现货白银","UKOIL":"布伦特油","USOIL":"美国原油","GC":"COMEX金交易量[分]","EURUSD":"欧元美元","GBPUSD":"英镑美元","USDJPY":"美元日元","AUDUSD":"澳元美元","USDCHF":"美元瑞郎","EURGBP":"欧元英镑","EURJPY":"欧元日元","XPDUSD":"现货钯金","DXY":"美元指数","DOWI":"道琼斯","NASX":"纳斯达克","SPX500":"标普500","JPN225":"日经225","SZZZ":"上证综指","SZCZ":"深证成指","XPTUSD":"现货铂金"};

    var createPDOM=function(id,html,to) {
        var tdom=document.createElement("p");
        tdom.style["font-size"]="12px !important";
        tdom.style["margin"]="0";
        tdom.innerHTML=html+"：<span id=\""+id+"_B\"></span>&nbsp;|&nbsp;<span id=\""+id+"_P\"></span>";
        document.getElementById(to).appendChild(tdom);
        return true;
    }

    function msgbox(str){
        if (str.match("金十贵金属多空投票") || str.match("http://app.jin10.com/")) {
            return;
        };
        var arrmp = str.split("#");
        var type = arrmp[0];
        var im = arrmp[1];
        var sj = arrmp[2];
        var nr = arrmp[3];
        var a4 = arrmp[4];
        var a5 = arrmp[5];
        var a6 = arrmp[6];
        var a7 = arrmp[7];
        var a8 = arrmp[8];
        var a9 = arrmp[9];
        var addtext = "";
            
        if (arguments[1]) {
            var playSound=jQuery.cookie("economics");
            if (playSound==1 || (playSound==2 && im==0)) {
                document.getElementById("ring").play();
            }
        }

        if(type == "0") {
            sj = sj.substr(11,8);
            if(im == "0"){
                addtext = template1.replace("{color}"," style=\"color:red; font-weight: bold;\"").replace("{pic}","importantnews.png").replace("{time}",sj);  
            } else {
                addtext = template1.replace("{color}","").replace("{pic}","nomarlnews.png").replace("{time}",sj);
            }
            if(a4.length > 5 || a5.length >5 || a6.length >5) {
                //have url pic video
                if(a4.length > 5 && a5.length > 5 && a6.length > 5) {
                    addtext = addtext.replace("{have}","have_url_pic_video");
                } else if((a4.length > 5 && a5.length > 5) || (a5.length > 5 && a6.length > 5)) {
                    addtext = addtext.replace("{have}","have_url_pic");
                } else if(a4.length > 5 && a5.length > 5) {
                    addtext = addtext.replace("{have}","have_no_pic");
                } else if(a6.length > 5 && a4.length < 5 && a5.length < 5) {
                    addtext = addtext.replace("{have}","have_pic");
                }
                
                addtext = addtext.replace("{more}","<p style=\"padding: 0 !important;\">{more}</p>").replace("{more}","{more2}");
                
                if(a4.length > 5) {
                    nrTmp=nr.split("<br />");
                    nrTmp[0]=("<a href=\""+a4+"\" target=\"_blank\">"+nrTmp[0]+"<\/a>").replace(/链.*?→→ */,"");
                    nr=nrTmp.join("<br />");
                    //addtext = addtext.replace("{more2}","<a href=\""+a4+"\" target=\"_blank\">链接戳这里<\/a>{more3}");
                    addtext = addtext.replace("{more2}","{more3}");
                } else {
                    addtext = addtext.replace("{more2}","{more3}");
                }
                
                if(a5.length > 5) {
                    addtext = addtext.replace("{more3}","<a href=\""+a5+"\" target=\"_blank\"><img src=\"http://www.jin10.com/oem\/images\/video2.png\" style=\"padding-bottom:5px;\" border=\"0\" /><\/a>{more4}");
                } else {
                    addtext = addtext.replace("{more3}","{more4}");
                }
                
                if(a6.length > 5) {
                    addtext = addtext.replace("{more4}","<a href=\"http:\/\/image.jin10.com\/"+a6.replace("_lite","")+"\" target=\"_blank\"><img src=\"http:\/\/image.jin10.com\/"+a6+"\" style=\"padding-bottom:5px;\" border=\"0\" \/><\/a>");
                } else {
                    addtext = addtext.replace("{more4}","");
                }

            } else {
                addtext = addtext.replace("{more}","");
            }
            
            addtext=addtext.replace("{content}",nr);
            jQuery("#listarea").prepend(addtext.replace(/ *(width|height)=\"\d*\"/g,""));
        } else if(type == "1") {
            im = im.substr(0,5);
            if(a6 >= 3) {
                a7.replace("2","");
                addtext = template2.replace("{color}"," style=\"color:red; font-weight: bold;\"").replace("{pic}","importantdata.png").replace("{time}",a8.substr(11,8)).replace("{shijian}",im).replace("{mingcheng}",sj).replace("{qianzhi}",nr).replace("{yuqi}",a4).replace("{gongbu}",a5).replace("{xingji}",a6).replace("{yingxiang}",a7).replace("{auagoil}",sj.match(/EIA|API|钻井/g)?"原油":"金银");
            } else {
                addtext = template2.replace("{color}","").replace("{pic}","nomarldata.png").replace("{time}",a8.substr(11,8)).replace("{shijian}",im).replace("{mingcheng}",sj).replace("{qianzhi}",nr).replace("{yuqi}",a4).replace("{gongbu}",a5).replace("{xingji}",a6).replace("{yingxiang}",a7).replace("{yingxiang}",a7).replace("{auagoil}",sj.match(/EIA|API|钻井/g)?"原油":"金银");
            }

            jQuery("#listarea").prepend(addtext);
        }//else if(type == "1")
    }//function msgbox(str){

    jQuery(function() {
        var varietyArr="<?php echo $variety; ?>".split(",");
        for (var varietyArri=0;varietyArri<varietyArr.length;varietyArri++) {
            createPDOM(varietyArr[varietyArri],jin10VarietyArr[varietyArr[varietyArri]],"variety");
        }
        jQuery("#listarea li").css("border-top","black 1px");
        jQuery("#listarea").parent().css({"background-color":"#ffffff","padding-bottom":"5px"});
        var timer = {};

        var Psocket = io.connect("ws://<?php echo $server; ?>");
        Psocket.on('connect' , function() {
            Psocket.emit('delAllSubscription' , []);
            Psocket.emit('addSubscription' , ['XAUUSD' , 'XAGUSD']);
            Psocket.emit('reqvote', "ok");
        });
        Psocket.on('price list', function(msg) {
            var oldvalue = jQuery('#' + msg.name + "_B").text();
            var newvalue = msg.v.lp;
            var pcolor;
            var ycolor = 'grey';

            if (Number(newvalue) > Number(oldvalue)) {
                pcolor = "#dc5538";
            } 
            else if (Number(newvalue) < Number(oldvalue)) {
                pcolor = "#238859";
            } 
            else if (Number(newvalue) == Number(oldvalue)) {
                return;
            }

            if (msg.v.ch != null) {
                var per = msg.v.ch / (msg.v.lp-msg.v.ch) * 100;
                if (Number(per) > 0) {
                    ycolor = "#FF0000";
                    jQuery('#' + msg.name + "_B").css("color", "#FF0000");
                    jQuery('#' + msg.name + "_P").css("color", "#FF0000");
                } 
                else if (Number(per) < 0) {
                    ycolor = "#0EA600";
                    jQuery('#' + msg.name + "_B").css("color", "#0EA600");
                    jQuery('#' + msg.name + "_P").css("color", "#0EA600");
                } 
                else {
                    ycolor = "#111111";
                    jQuery('#' + msg.name + "_B").css("color", "#111111");
                    jQuery('#' + msg.name + "_P").css("color", "#111111");
                }
                jQuery('#' + msg.name + "_P").text(per.toFixed(2) + "%");
            }

            var id = timer[msg.name];
            if (id != null && id != 0) {
                clearTimeout(id);
            };
            jQuery('#' + msg.name + "_B").css({
                "color": "white",
                "background-color": pcolor
            });
            jQuery('#' + msg.name + "_B").text(msg.v.lp);

            var mm = setTimeout(function () {
                jQuery('#' + msg.name + "_B").css({
                    "background-color": "transparent",
                    "color": jQuery('#' + msg.name + "_P").css('color')
                });
                timer[msg.name] = 0;
            }, 600);
            timer[msg.name] = mm;

        });
        Psocket.on('user message',function(msg){
            msgbox(msg,true);
        });
        Psocket.on('disconnect', function() {
            jQuery("#listarea").prepend("<li><p style=\"font-weight: bold; color: red;\">Connection Lost. Please refresh this page.</p></li>");
        });
    });
</script>
<?
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    function form( $instance ) {

        $defaults = array( 'title' => '财经消息 by 金十数据 jin10.com', 'server' => 'jshensh-jin10.daoapp.io', "variety"=>"XAUUSD,XAGUSD" );
        $instance = wp_parse_args( (array) $instance, $defaults ); 

        $addedID=str_replace("-","",$this->get_field_id( 'jin10Added' ));
        $notAddID=str_replace("-","",$this->get_field_id( 'jin10NotAdd' ));
        $varietyID=$this->get_field_id( 'variety' );
        ?>

        <link rel="stylesheet" href="<?php echo plugins_url('style.css',__FILE__) ; ?>">

        <input type="hidden" name="<?php echo $this->get_field_name( 'variety' ); ?>" id="<?php echo $varietyID; ?>" value="<?php echo $instance['variety']; ?>" />

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo '标题:'; ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'server' ); ?>"><?php _e('服务器地址:', 'example'); ?></label>
            <input id="<?php echo $this->get_field_id( 'server' ); ?>" name="<?php echo $this->get_field_name( 'server' ); ?>" value="<?php echo $instance['server']; ?>" style="width:100%;" />
        </p>

        <div style="">
        <div data-force="30" class="layer block">
            <div class="layer title">未添加</div>
            <ul id="<?php echo $notAddID; ?>" class="block__list block__list_tags">
            </ul>
        </div>

        <div data-force="18" class="layer block">
            <div class="layer title">已添加</div>
            <ul id="<?php echo $addedID; ?>" class="block__list block__list_tags">
            </ul>
        </div>
    </div>

    <script src="<?php echo plugins_url('Sortable.js',__FILE__) ; ?>"></script>

    <script>
        (function (){
            var console = window.console;

            if( !console.log ){
                console.log = function (){
                    alert([].join.apply(arguments, ' '));
                };
            }

            var jin10NotAddArr={"XAUUSD":"现货黄金","XAGUSD":"现货白银","UKOIL":"布伦特油","USOIL":"美国原油","GC":"COMEX金交易量[分]","EURUSD":"欧元美元","GBPUSD":"英镑美元","USDJPY":"美元日元","AUDUSD":"澳元美元","USDCHF":"美元瑞郎","EURGBP":"欧元英镑","EURJPY":"欧元日元","XPDUSD":"现货钯金","DXY":"美元指数","DOWI":"道琼斯","NASX":"纳斯达克","SPX500":"标普500","JPN225":"日经225","SZZZ":"上证综指","SZCZ":"深证成指","XPTUSD":"现货铂金"};
            var jin10AddedArr=document.getElementById("<?php echo $varietyID; ?>").value.split(",");

            var createLiDOM=function(id,html,dom) {
                var tdom=document.createElement("li");
                tdom.id=id;
                tdom.innerHTML=html;
                dom.appendChild(tdom);
                return true;
            }

            var removeAllChild=function(dom) {
                while(dom.hasChildNodes()) {
                    dom.removeChild(dom.firstChild);
                }
            }

            var div = document.getElementsByTagName('div');
            var ulEle=div[div.length - 1].parentNode.parentNode.getElementsByTagName("ul");
            var conUl=(function(ulEle) {
                var varietyID=ulEle[ulEle.length-2].id.match(/^widgetjin10widget(\d*?)j/);
                if (varietyID) {
                    var variety=document.getElementById("widget-jin10-widget-"+varietyID[1]+"-variety");
                    if (ulEle[ulEle.length-1].id=="") {
                        return [ulEle[ulEle.length-2],ulEle[ulEle.length-3],variety];
                    } else {
                        return [ulEle[ulEle.length-1],ulEle[ulEle.length-2],variety];
                    }
                }
                return false;
            })(ulEle);
            if (!conUl) {
                return false;
            }

            removeAllChild(conUl[0]);
            removeAllChild(conUl[1]);

            for (var i=0;i<jin10AddedArr.length;i++) {
                createLiDOM(jin10AddedArr[i],jin10NotAddArr[jin10AddedArr[i]],conUl[0]);
                delete jin10NotAddArr[jin10AddedArr[i]];
            }

            for (var i in jin10NotAddArr) {
                createLiDOM(i,jin10NotAddArr[i],conUl[1]);
            }

            var updateVarietyValue=function() {
                var dom=conUl[0].childNodes;
                var tArr=[];
                for (var i=0;i<dom.length;i++) {
                    if (typeof dom.item(i).id!=="undefined") {
                        tArr.push(dom.item(i).id);
                    }
                }
                conUl[2].value=tArr.join(",");
            }

            new Sortable(conUl[0], {
                group: "words",
                onAdd: function (evt){ updateVarietyValue(); },
                onUpdate: function (evt){ updateVarietyValue(); },
                onRemove: function (evt){ updateVarietyValue(); }
            });
            new Sortable(conUl[1], {
                group: "words",
                onAdd: function (evt){ updateVarietyValue(); },
                onUpdate: function (evt){ updateVarietyValue(); },
                onRemove: function (evt){ updateVarietyValue(); }
            });
        })();
    </script>

    <?php
    }
}

?>