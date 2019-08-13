/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

Aitoc_Aitholiday.Decoration.ImgObserver = Class.create(Abstract.TimedObserver,{
            
    _value: 1 ,
    
    getValue: function()
    {
        switch (this._value)
        {
            case 1:
                if (0 != this.element.getWidth())
                {
                    this._value = 2;
                }
                return false;
            case 2:
                return true;
        }
    }
});

Aitoc_Aitholiday.Decoration.Manager = Class.create(
{
    
    _isAdminMode: false ,
    
    _controls: null ,
    
    _request: null ,
    
    _showDecoration: null ,
    
    _updateInProgress: false ,
    
    _canIgnoreParams: null ,
    
    initialize: function ( isAdmin )
    {
        this._controls = {};
        this._isAdminMode = isAdmin || false;
        this._request = new Aitoc_Aitholiday.User.Request(this);
        this._initPositionObserver();
    } ,
    
    _initPositionObserver: function()
    {
        var positionObserverClass = Class.create(Abstract.TimedObserver,{
            getValue: function()
            {
                return document.viewport.getWidth();
            }
        });
        new positionObserverClass(document,0.1,function()
        {
            this.getControls().each(function( control )
            {
                control.offsetToPosition();
            }.bind(this));
        }.bind(this));
    } ,
    
    showDecoration: function (show)
    { 
        if ('undefined' == typeof show)
        {
            this._showDecoration = true;
        }
        else
        {
            this._showDecoration = show;
        }
    } ,
    
    isAdminMode: function()
    {
        return this._isAdminMode;
    } ,
    
    registerControl: function( control )
    {
        this._controls[control.getId()] = control;
    } ,
    
    unregisterControl: function( control )
    {
        var id = control.getId();
        if (this._controls[id])
        {
            delete this._controls[id];
        }
    } ,
    
    getControls: function()
    {
        return $H(this._controls).values();
    } ,
    
    update: function( callback )
    {
        if (this._updateInProgress)
        {
            return;
        }
        this._updateInProgress = true;
        this.getControls().each(function( item )
        {
            item.destroy();
        });
        this._request.send([],function( response )
        {
            if (callback)
            {
                callback(response);
            }
            this._request.realizeResponse(response);
        }.bind(this));
    } ,
    
    realizeResponse: function( response )
    {
        if (!this._showDecoration)
        {
            return;
        }
        if (response.responseJSON && response.responseJSON.items)
        {
            var data = response.responseJSON.items;
            this._canIgnoreParams = response.responseJSON.canIgnoreParams; 
            data.each(this.drawControl.bind(this));
            this._updateInProgress = false;
        }
    } ,
    
    canIgnoreParams: function()
    {
        return this._canIgnoreParams; 
    } ,
    
    drawControl: function( item )
    {
        var img = null;
        var elem = $(Builder.node('div',{
            id: item.id ,
            className: 'aitholiday-item'
        },[
           img = $(Builder.node('img',{
               src: Aitoc_Aitholiday.baseUrl+item.url
           }))
        ]));
        Aitoc_Aitholiday.getBody().insert(elem);

        var currentObserver = new Aitoc_Aitholiday.Decoration.ImgObserver(img,0.2,function()
        {
            if (this.isAdminMode())
            {
                var control = new Aitoc_Aitholiday.Decoration.AdminControl(this,elem);
                control.setImageFile(item.file);
                control.setIsPublic(item.is_public);
                control.useParams(item.use_params);
                elem.addClassName('aitholiday-admin');
            }
            else
            {
                var control = new Aitoc_Aitholiday.Decoration.Control(this,elem);
            }
            var style = "position: absolute; z-index: "+item.z_index;
            elem.setStyle(style);
            control.setOffset(item.position.x,item.position.y);
            control.setScale(item.scale);
            control.setRealId(item.real_id);
            currentObserver.stop();
            delete currentObserver;
        }.bind(this));
    } ,
    
    getUpdateCallback: function()
    {
        return this.update.bind(this);
    } ,
    
    controlsToObject: function()
    {
        var data = {};
        this.getControls().each(function( control )
        {
            var id = control.getId();
            if (id)
            {
                control.positionToOffset();
                data[id] = {
                    id: control.getRealId() ,
                    scale: control.getScale() ,
                    file: control.getImageFile() ,
                    is_public: control.isPublic() ,
                    position: {
                        x: control.getX() ,
                        y: control.getY()
                    } ,
                    is_new: control.isNew() ,
                    use_params: control.isParamsUsed()
                };
            }
            else if ((id = control.getRealId()) && !control.isNew())
            {
                data[id] = {
                    id: id ,
                    is_deleted: control.isDeleted()
                };
            }
        }.bind(this));
        return data;
    }
    
});

Aitoc_Aitholiday.Decoration.Abstract = Class.create(
{
    
    _element: null ,
    
    _img: null ,
    
    _isNew: false ,
    
    _id: null ,
    
    _x: null ,
    
    _y: null ,
    
    _fixPNG: function (element)
    {
        if (/MSIE (5\.5|6).+Win/.test(navigator.userAgent))
        {
            var src;
            
            if (element.tagName=='IMG')
            {
                if (/\.png$/.test(element.src))
                {
                    src = element.src;
                    element.src = Aitoc_Aitholiday.baseUrl + "/skin/frontend/default/default/images/aitoc/aitholiday/blank.gif";
                }
            }
            else
            {
                src = element.currentStyle.backgroundImage.match(/url\("(.+\.png)"\)/i)
                if (src)
                {
                    src = src[1];
                    element.runtimeStyle.backgroundImage="none";
                }
            }
            
            if (src) element.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "',sizingMethod='scale')";
        }
    } ,
    
    setRealId: function( id )
    {
        this.setIsNew(false);
        this._id = id;
    } ,
    
    getRealId: function()
    {
        return this._id;
    } ,
    
    setIsNew: function( isNew )
    {
        if ('undefined' == typeof isNew)
        {
            this._isNew = true;
        }
        else
        {
            this._isNew = !!isNew;
        }
    } ,
    
    isNew: function()
    {
        return this._isNew;
    } ,
    
    initialize: function( elem )
    {
        this._element = $(elem);
        this._init();
    } ,
    
    getId: function()
    {
        if (!this._element)
        {
            return null;
        }
        return this._element.getAttribute('id');
    } ,
    
    _init: function()
    {
        this._img = this._element.select('img')[0];
        var currentObserver = new Aitoc_Aitholiday.Decoration.ImgObserver(this._img,0.2,function()
        {
            this._fixPNG(this._img);
            currentObserver.stop();
            delete currentObserver;
        }.bind(this));
    } ,
    
    getWidth: function()
    {
        return this._img.clientWidth;
    } ,
    
    getHeight: function()
    {
        return this._img.clientHeight;
    } ,
    
    getX: function()
    {
        return this._x;
    } ,
    
    getY: function()
    {
        return this._y;
    } ,
    
    setOffset: function ( x , y )
    {
        this._x = parseInt(x);
        this._y = parseInt(y);
        this.offsetToPosition();
    } ,
    
    offsetToPosition: function( x , y , onlyReturn )
    {
        if (!x || !y)
        {
            x = this._x;
            y = this._y;
        }
        var width = document.viewport.getWidth();
        var top = y;
        var left = width/2 + x;
        if (!onlyReturn)
        {
             this._element.setStyle('left: '+left+"px;");
             this._element.setStyle('top: '+top+"px;");
        }
        return [left,top];
    } ,
    
    positionToOffset: function( left , top , onlyReturn )
    {
        if (!left || !top)
        {
            left = this.getLeft();
            top = this.getTop();
        }
        var width = document.viewport.getWidth();
        var y = top;
        var x = left - width/2;
        if (!onlyReturn)
        {
            this._x = x;
            this._y = y;
        }
        return [x,y];
    } ,
    
    getLeft: function()
    {
        return parseInt(this._element.getStyle('left'));
    } ,
    
    getTop: function()
    {
        return parseInt(this._element.getStyle('top'));
    } ,
    
    updateView: function()
    {
    } ,
    
    clearView: function()
    {
    } ,
    
    destroy: function()
    {
        this.clearView();
        if (this._element)
        {
            this._element.remove();
            delete this._img;
            delete this._element;
        }
    }
    
});

Aitoc_Aitholiday.Decoration.Control = Class.create(Aitoc_Aitholiday.Decoration.Abstract,
{
    
    _manager: null ,
    
    _imageFile: "" ,
    
    _baseWidth: 0 , 
    
    getImageFile: function()
    {
        return this._imageFile;
    } ,
    
    initialize: function( $super , manager , elem )
    {
        this._manager = manager;
        $super(elem);
        this._manager.registerControl(this);
        this._baseWidth = this.getWidth();
    } ,
    
    setScale: function ( scale )
    {
        var width = this._baseWidth * scale / 100;
        var style = "width: "+width.round()+"px;";
        this._img.setStyle(style);
        this._element.setStyle(style);
    } ,
    
    destroy: function( $super )
    {
        this._manager.unregisterControl(this);
        $super();
    }
    
});

Aitoc_Aitholiday.Decoration.AdminControl = Class.create(Aitoc_Aitholiday.Decoration.Control,
{   
    _wrapper: null ,
    
    _resizer: null ,
    
    _deleteButton: null ,
    
    _useParamsCheckbox: null ,
    
    _publicCheckbox: null ,
    
    _isPublic: false ,
    
    _panel: null ,
    
    _useParams: null ,
    
    _isDeleted: false ,
    
    isDeleted: function()
    {
        return this._isDeleted;
    } ,
    
    setIsPublic: function( isPublic )
    {
        if ('undefined' == typeof isPublic)
        {
            this._isPublic = true;
        }
        else
        {
            this._isPublic = isPublic;
        }
        this._syncPublicator();
    } ,
    
    useParams: function( useParams )
    {
        if ('undefined' == typeof useParams)
        {
            this._useParams = true;
        }
        else
        {
            this._useParams = useParams;
        }
        this._syncUseParams();
    } ,
    
    isParamsUsed: function()
    {
        return this._useParams;
    } ,
    
    _syncPublicator: function()
    {
        if (this._publicCheckbox)
        {
            this._publicCheckbox.checked = this._isPublic;
            if (this._isPublic)
            {
                this.useParams(true);
            }
        }
    } ,
    
    _syncUseParams: function()
    {
        if (this._useParamsCheckbox)
        {
            this._useParamsCheckbox.checked = !this._useParams;
            if (!this._useParams)
            {
                this.setIsPublic(false);
            }
        }
    } ,
    
    isPublic: function()
    {
        return this._isPublic;
    } ,
    
    getCurrentWidth: function()
    {
        return jQuery(this._resizer).slider('value');
    } ,
    
    getScale: function()
    {
        return (100 * this.getCurrentWidth()/this._baseWidth).round();
    } ,
    
    setScale: function( $super , scale )
    {
        var value = (this.getWidth()/100*scale).round();
        jQuery(this._resizer).slider('value',value);
        this._img.setStyle("width: "+value+"px;");
        this.updatePanelPosition();
    } ,
    
    setImageFile: function( file )
    {
        this._imageFile = file;
    } ,
    
    _createResizer: function()
    {
        this._baseWidth = this.getWidth();
        jQuery(this._resizer).slider({
            min: 0 ,
            max: this.getWidth() ,
            value: this.getWidth() ,
            slide: function( event , ui ) 
            {
                this._img.setStyle("width: "+ui.value+"px");
                this.updatePanelPosition();
            }.bind(this)
        });
    } ,
    
    updatePanelPosition: function()
    {
        var left = -this._panel.getWidth()-5;
        var top = -this._img.getHeight()+10;
        this._wrapper.setStyle("left: "+left+"px; top:"+top+"px;");
    } ,
    
    _addDraggable: function()
    {
        jQuery(this._element).draggable({
            handle: this._img ,
            stop: function(){this.positionToOffset();}.bind(this)
        });
    } ,
    
    _addDeleteButton: function()
    {
        this._deleteButton.observe('click',this.onClickDelete.bind(this));
    } ,
    
    _addUseParams: function()
    {
        this._useParamsCheckbox.observe('click',this.onClickUseParams.bind(this));
        this._syncUseParams();
    } ,
    
    onClickDelete: function()
    {
        this._isDeleted = true;
        this.clearView();
        this._element.remove();
        delete this._element;
        delete this._img;
    } ,
    
    _addPublicator: function()
    {
        this._publicCheckbox.observe('click',this.onClickPublicator.bind(this));
        this._syncPublicator();
    } ,
    
    onClickUseParams: function()
    {
        this.useParams(!this._useParamsCheckbox.checked);
    } ,
    
    onClickPublicator: function()
    {
        this.setIsPublic(this._publicCheckbox.checked);
    } ,
    
    _init: function( $super )
    {
        $super();
        var paramsCheckBox = [];
        
        if (this._manager.canIgnoreParams())
        {
            paramsCheckBox = [ Aitoc_Aitholiday.language.ignore_url_params ,
            this._useParamsCheckbox = $(Builder.node('input',{
                type: 'checkbox' ,
                id: this.getId()+'_use_params' ,
                value: "1"
            }))];
        }
        
        this._wrapper = $(Builder.node('div',{
            id: this.getId()+"_wrapper", 
            className: 'aitholiday-wrapper'
        }, this._panel = $(Builder.node('div',{
                className: 'aitholiday-panel'
            } , [
                this._deleteButton = $(Builder.node('div',{
                    id: this.getId()+"_deleteButton" ,
                    className: 'aitholiday-delete'
                }," ")) ,
                this._resizer = $(Builder.node('div',{
                    id: this.getId()+"_resizer" ,
                    className: 'aitholiday-resizer'
                })) ,
                $(Builder.node('div',{
                    className: "aitholiday-publicator"
                },[
                   Aitoc_Aitholiday.language.public ,
                   this._publicCheckbox = $(Builder.node('input',{
                       type: 'checkbox' ,
                       id: this.getId()+'_publicator' ,
                       value: "1"
                   }))
                ])) ,
                $(Builder.node('div',{
                    className: "aitholiday-publicator"
                },paramsCheckBox))
            ]))
        ));
        this._element.insert(this._wrapper);
        this._createResizer();
        this._addDraggable();
        this._addDeleteButton();
        this._addPublicator();
        if (this._manager.canIgnoreParams())
        {
            this._addUseParams();
        }
        this.updatePanelPosition();
    }
    
});


Aitoc_Aitholiday.Decoration.PaletteControl = Class.create(Aitoc_Aitholiday.Decoration.Abstract,
{
    
    _palette: null ,
    
    initialize: function( $super , palette , elem )
    {
        $super(elem);
        this._palette = palette;
        this._imageFile = elem.getAttribute('imagefile');
    } ,
    
    _draggable: null ,

    updateView: function( $super )
    {
        $super();
        jQuery(this._element).draggable({
            handle: this._element,
            helper: 'clone' ,
            scroll: true ,
            start: this.onStartDrag.bind(this),
            stop: this.onEndDrag.bind(this),
            revert: true ,
            revertDuration: 0
        });
    } ,
    
    onEndDrag: function( event )
    {
        this._element.removeClassName("dragged");
        this._createControl(event);
    } ,
    
    onStartDrag: function( elem , event )
    {
        this._element.addClassName("dragged");
    } ,
    
    _createControl: function( event )
    {
        var img = $(Builder.node('img',{
            src: this._img.getAttribute('src')
        }));
        var id = this._element.id + '_new_'+this._palette.createNextId(); 
        var elem = $(Builder.node('div',{
            id: id ,
            className: 'aitholiday-item'
        },[img]));
        elem.setStyle("position: absolute; left: "+Event.pointerX(event)+"px; top: "+Event.pointerY(event)+"px; z-index: 9998;");
        Aitoc_Aitholiday.getBody().insert(elem);
        var currentObserver = new Aitoc_Aitholiday.Decoration.ImgObserver(img,0.2,function()
        {
            var control = new Aitoc_Aitholiday.Decoration.AdminControl(this._palette.getDecorationManager(),elem);
            control.setIsNew();
            control.useParams(true);
            control.setImageFile(this._imageFile);
            control.positionToOffset();
            currentObserver.stop();
            delete currentObserver;
        }.bind(this));
    } ,

    clearView: function ( $super )
    {
        $super();
        jQuery(this._element).draggable('destroy');
    }

});