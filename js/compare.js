/* $Id: compare.js 15469 2008-12-19 06:34:44Z testyang $ */
var Compare = new Object();

Compare = {
  add : function(goodsId, goodsName, type, imgurl)
  {
    var count = 0;
    for (var k in this.data)
    {
      if (typeof(this.data[k]) == "function")
      continue;
      if (this.data[k].t != type) {
		  this.compareBox.style.display = "";
        alert(goods_type_different.replace("%s", goodsName));
        return;
      }
      count++;
    }

    if (this.data[goodsId])
    {
      this.compareBox.style.display = "";
	  alert(exist.replace("%s",goodsName));
      return;
    }
    else
    {
      this.data[goodsId] = {n:goodsName,t:type,i:imgurl?imgurl:''};
    }
    this.save();
    this.init();
  },
  relocation : function()
  {
    if (this.compareBox.style.display != "") return;
    var diffY = Math.max(document.documentElement.scrollTop,document.body.scrollTop);

    var percent = .2*(diffY - this.lastScrollY);
    if(percent > 0)
      percent = Math.ceil(percent);
    else
      percent = Math.floor(percent);
    this.compareBox.style.top = parseInt(this.compareBox.style.top)+ percent + "px";

    this.lastScrollY = this.lastScrollY + percent;
  },
  init : function(){
    this.data = new Object();
    var cookieValue = document.getCookie("compareItems");
    if (cookieValue != null) {
      this.data = $.evalJSON(cookieValue);
    }
    if (!this.compareBox)
    {
      this.compareBox = document.createElement("DIV");
      var submitBtn = document.createElement("INPUT");
      this.compareList = document.createElement("UL");
      this.compareBox.id = "compareBox";
      this.compareBox.style.display = "none";
      this.compareBox.style.top = "160px";
      this.compareBox.align = "center";
      this.compareList.id = "compareList";
      submitBtn.type = "button";
      submitBtn.value = button_compare;
	  
      this.compareBox.onclick = function(){
        this.style.display = 'none';
      }
	  
		this.compareBox.appendChild(this.compareList);
		this.compareBox.appendChild(submitBtn);
      submitBtn.onclick = function() {
        var cookieValue = document.getCookie("compareItems");
        var obj = $.evalJSON(cookieValue);
        var url = document.location.href;
        url = url.substring(0,url.lastIndexOf('/')+1) + "compare.php";
        var i = 0;
        for(var k in obj)
        {
          if(typeof(obj[k])=="function")
          continue;
          if(i==0)
            url += "?goods[]=" + k;
          else
            url += "&goods[]=" + k;
          i++;
        }
        if(i<2)
        {
          alert(compare_no_goods);
          return ;
        }
        document.location.href = url;
      }
      document.body.appendChild(this.compareBox);
    }
    this.compareList.innerHTML = "";
    var self = this;
    for (var key in this.data)
    {
      if(typeof(this.data[key]) == "function")continue;
		
      var li = document.createElement("LI");
	  li.className = 'clearfix';
      li.style.width = "116px";
      li.style.height = "40px";
      li.style.listStyle = "none";
      li.style.position = "relative";
	  
      var span = document.createElement("SPAN");
      span.innerHTML = this.data[key].n;
      span.style.overflow = "hidden";
      span.style.width = "62px";
      span.style.height = "40px";
      span.style.display = "block";
      li.appendChild(span);
	  
	  //goodsName start
	  var spanDIV = document.createElement("DIV"); 
	  spanDIV.style.left = "50px";    
	  spanDIV.style.width = "72px";
	  spanDIV.style.height = "40px";
	  spanDIV.style.position = "absolute";
	  spanDIV.appendChild(span);
	  li.appendChild(spanDIV);
	  //goodsName end
	  
      var delBtn = document.createElement("IMG");
      delBtn.src = "themes/default/images/drop.gif";
      delBtn.className = key;
      delBtn.onclick = function(){
        document.getElementById("compareList").removeChild(this.parentNode.parentNode);
        delete self.data[this.className];
        self.save();
        self.init();
      }
      spanDIV.insertBefore(delBtn,span);//By goodsName
      //li.insertBefore(delBtn,li.childNodes[0]);//By Old
	  
	  //goodsIMG start
	  var DIV = document.createElement("DIV");
	  DIV.style.left = "2px";
	  DIV.style.width = "40px";
	  DIV.style.height = "40px";
	  DIV.style.padding = "0px";
	  DIV.style.background = "#EEE";
	  DIV.style.position = "absolute";
	  if(this.data[key].i){
		  var goodsIMG = document.createElement("IMG");
		  goodsIMG.src = this.data[key].i;
		  goodsIMG.width = 40;
		  goodsIMG.height = 40;
		  goodsIMG.style.padding = "0px";
		  DIV.appendChild(goodsIMG);
	  }
	  li.insertBefore(DIV,li.childNodes[0]);
	  //goodsIMG end
	  
      this.compareList.appendChild(li);
    }
    if (this.compareList.childNodes.length > 0)
    {
      this.compareBox.style.display = "";
      this.timer = window.setInterval(this.relocation.bind(this), 50);
    }
    else
    {
      this.compareBox.style.display = "none";
      window.clearInterval(this.timer);
      this.timer = 0;
    }
  },
  save : function()
  {
    var date = new Date();
    date.setTime(date.getTime() + 99999999);
    document.setCookie("compareItems", $.toJSON(this.data));
  },
  lastScrollY : 0
}