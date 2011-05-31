function Ajax(){
	this.loading	= null;
	this.complete	= null;

	this.ST_UNINITIALIZED	= 0;
	this.ST_LOADING		= 1;
	this.ST_LOADED		= 2;
	this.ST_INTERACTIVE		= 3;
	this.ST_COMPLETE		= 4;


	this.constructor = function(){
		this.conn 		= this.getXmlHttpObject(this);
		this.method     = "POST";
		this.url        = "";
		this.vars       = "";
		this.vKeys		= new Array();
		this.vValues	= new Array();
		this.iVars		= 0;
		this.status		= 0;
		this.intervalo	= 0;

		this.bAsync	= true;
	};

	this.getXmlHttpObject = function(par){
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
		try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
		xmlhttp = false;
		}
		}
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			try {
				xmlhttp = new XMLHttpRequest();
			} catch (e) {
				xmlhttp=false;
			}
		}
		if (!xmlhttp && window.createRequest) {
			try {
				xmlhttp = window.createRequest();
			} catch (e) {
				xmlhttp=false;
			}
		};
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==par.ST_LOADING){
				if(par.loading){
					par.loading(xmlhttp,par);
				}
			}

			if(xmlhttp.readyState==par.ST_COMPLETE){
				if(par.complete){
					par.complete(xmlhttp,par);
				}
			}


		};
		return xmlhttp;
	};


	this.clearVars = function(){
		this.vars = "";
		this.vkeys = new Array();
		this.vValues = new Array();
		this.iVars	= 0;

	};

	this.setVar=function(key,value){
		this.vars += key + "=" + value + "&";
		this.vKeys[this.iVars] = key;
		this.vValues[this.iVars] = value;
		this.iVars++;
	};

	this.getVar=function(sKey){
		for(i=0;i<this.iVars;i++){
			if(this.vKeys[i] == sKey){
				return this.vValues[i];
				break;
			}
		}
	};

	this.setInterval = function(inter){
		this.intervalo = inter;
	};

	this.getInterval = function(){
		return this.intervalo;
	};

	this.setMethod=function(sMethod){
		this.method = sMethod.toUpperCase();
	};

	this.setAsync=function(bAs){
		this.bAsync=bAs;
	};

	this.setUrl=function(sURL){
		this.url=sURL;
	};
	
	this.getUrl=function(){
		return this.url;
	};

	this.setOnLoading=function(hnd){
		this.loading = hnd;
	};

	this.setOnComplete=function(hnd){
		this.complete = hnd;
	}

	this.connect=function(){
		try{
			if(!this.conn)
			this.conn 	= this.getXmlHttpObject(this);
			
			if(this.method=="GET"){
				this.conn.open(this.method,this.url+"?"+this.vars,this.bAsync);
				this.vars=null;
			}else{
				this.conn.open(this.method,this.url,this.bAsync);
				this.conn.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2005 00:00:00 GMT");
				this.conn.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

			}
		}catch(e){
			if(e.message!=undefined)
			alert(e.message);
			else
			alert(e);
		}
	}

	this.getData=function(){
		try{
			if(!this.conn){
				this.conn 	= this.getXmlHttpObject(this);
				this.connect();
			}

			this.conn.send(this.vars);
		}catch(e){
			if(e.message!=undefined)
			alert(e.message);
			else
			alert(e);
		}
	}

	try{
		this.constructor();

	}catch(e){
		alert(e);
	}

	return this;
}
