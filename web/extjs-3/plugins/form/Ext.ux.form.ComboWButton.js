/**
 * Different from Extjs-2 Version
 * Do not overwrite the full code
 */
/**
* @author radu
*/
Ext.ns('Ext.ux.form'); 
/** 
 * @class Ext.ux.form.ComboWButton
 * @extends Ext.form.Field
 */
Ext.ux.form.ComboWButton = Ext.extend(Ext.form.Field,  {
	/**
	* buttonConfig:{text,icon},
	* windowConfig:{title,component: extJs Object private name var,class,method}
	*/
	readOnly:false,
    bodyStyle:null,
    border:true,
    oneLineLayout:true,
    defaultAutoCreate:{tag: "div"},
        
    initComponent: function(){
        Ext.ux.form.ComboWButton.superclass.initComponent.call(this);         
    },

    onRender: function(ct, position){
        Ext.ux.form.ComboWButton.superclass.onRender.call(this, ct, position);		
        this.comboConfig = this.comboConfig || {};
        Ext.applyIf(this.comboConfig, {
			forceSelection: this.forceSelection,
			disableKeyFilter: this.disableKeyFilter,
			mode: this.mode,
			triggerAction: this.triggerAction,
			width:this.width-30,
			store:this.store,
			value:this.value,
			hiddenName:this.hiddenName
		});
		
			
		this.button = new Ext.Button({			
			iconCls: 'icon-plus',
			tooltip: 'Not in list? click to add one'
		});
		this.button.on({
			click: {scope:this,fn:this.onClick}
		});
		this.windowConfig = this.windowConfig || {};		
		this.combo = new Ext.form.ComboBox(this.comboConfig);		
		var p = new Ext.Panel({
            bodyStyle:this.bodyStyle,
            border:this.border,
            layout:"table",
            layoutConfig:{columns:3}
            
        });
		
		
		var mainDiv = Ext.DomHelper.insertFirst(ct, {tag: 'div', style:'width:'+this.width+'px; margin:0px; padding:0px'});		
		var comboDiv = Ext.DomHelper.append(mainDiv, {tag: 'div', style:'float:left;margin:0px; padding:0px'});
		var buttonDiv = Ext.DomHelper.append(mainDiv, {tag: 'div', style:'float:left; margin:0px; margin-left:8px; padding:0px'});
		Ext.DomHelper.append(mainDiv, {tag: 'div', style:'clear:both;'});
		//this.mainDiv = mainDiv;
		this.combo.render(comboDiv);
		this.button.render(buttonDiv);
       // p.add(this.combo);
        //p.add({html:'&nbsp;&nbsp;'});
        //p.add(this.button);
        //p.render(this.el);
       
        
       /// var tb = p.body.first();
       // this.el.setWidth(p.body.getWidth());
       // p.body.removeClass();
    },
	markInvalid : function(msg){	
        //don't set the error icon if we're not rendered or marking is prevented
		
		/*
		* Fix for the mark invalid red border and icon
		*/
		this.el = this.combo.getEl();		
		//*********************************************/
        if (this.rendered && !this.preventMark) {
            msg = msg || this.invalidText;
            var mt = this.getMessageHandler();			
            if(mt){
                mt.mark(this, msg);				
				/*
				* Fix for the mark invalid red border and icon
				*/
				var errorIcon = Ext.select(".x-form-invalid-icon",false,this.getErrorCt().dom);				
				if(errorIcon && errorIcon.elements && errorIcon.elements[0]){
					errorIcon.elements[0].style.left = errorIcon.elements[0].offsetLeft + 47 +"px";
					errorIcon.elements[0].style.display = "block";
					this.eI = errorIcon.elements[0];
				}				
				//*********************************************/
            }else if(this.msgTarget){			
                this.el.addClass(this.invalidClass);
                var t = Ext.getDom(this.msgTarget);
                if(t){
                    t.innerHTML = msg;
                    t.style.display = this.msgDisplay;
                }
            }
        }        
        this.setActiveError(msg);
    },
	clearInvalid : function(){
        //don't remove the error icon if we're not rendered or marking is prevented
		if(this.eI){
			this.eI.style.display="none";
			
		}
		this.el = this.combo.getEl();			
        if (this.rendered && !this.preventMark) {
            this.el.removeClass(this.invalidClass);
            var mt = this.getMessageHandler();
            if(mt){
                mt.clear(this);	
				mt.clear(this.combo);
				
            }else if(this.msgTarget){
                this.el.removeClass(this.invalidClass);
                var t = Ext.getDom(this.msgTarget);
                if(t){
                    t.innerHTML = '';
                    t.style.display = 'none';
                }
            }
        }		
       
        this.unsetActiveError();
    },
    
    initValue:Ext.emptyFn,
    
	onClick: function(button,event){
		/*
		 * Using the forms with name edit[99][] for multiple forms in same page will have problem in action page.
		 * Even using a singel edit[99][] form, we need to change the action for the respective widget, since
		 * it listens to edit[0][].
		 * 
		 * Now using the afApp.widgetPopup will use the edit[0][] for every form and actions need not be changed.
		 */
		afApp.widgetPopup(this.windowConfig.component,this.windowConfig.title?this.windowConfig.title:'Add combo option',this)			
	},
	
	onHide: function(window){
		var combo = this.combo;
		if(this.windowConfig.className&&this.windowConfig.methodName)
		{
			Ext.Ajax.request({ 
				url: "/appFlower/getComboOptions", 
				method:"post", 
				params: {
					'class':this.windowConfig.className,
					'method':this.windowConfig.methodName}, 
					success:function(response, options){						
						response=Ext.decode(response.responseText);
						var s = eval(response.store);
						combo.bindStore(s);					
						var last = s[s.length-1];   
						if(last[1]){
							combo.setValue(last[0]);
						}
				},
				failure: function(response,options) {
					Ext.Msg.alert("Failure","Could not retrieve combo options !");
				},
				scope:this
			});
		}
	},
	    
    getValue : function() {
        return this.combo.getValue();
    }
});

Ext.reg("combowbutton", Ext.ux.form.ComboWButton);