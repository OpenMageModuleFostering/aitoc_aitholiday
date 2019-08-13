/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

Aitoc_Aitholiday.Palette.Manager = Class.create(
{
    
    _paletteControls: null ,
    
    _decorationManager: null ,
    
    initialize: function()
    {
        this._paletteControls = {};
        this._decorationManager = new Aitoc_Aitholiday.Decoration.Manager(true);
        this._decorationManager.showDecoration();
    } ,
    
    createControl: function( id )
    {
        this._paletteControls[id] = new Aitoc_Aitholiday.Palette.Control(this,id);
    } ,
    
    getDecorationManager: function()
    {
        return this._decorationManager;
    }
    
});

Aitoc_Aitholiday.Palette.Abstract = Class.create(
{
    
    _manager: null ,
    
    initialize: function ( manager )
    {
        this._manager = manager;
    } ,
    
    getManager: function()
    {
        return this._manager;
    } ,
    
    getDecorationManager: function()
    {
        return this._manager.getDecorationManager();
    }
    
});

Aitoc_Aitholiday.Palette.Control = Class.create(Aitoc_Aitholiday.Palette.Abstract,
{
    
    _request: null ,
    
    _body: null ,
    
    _id: null ,
    
    _drawed: false ,
    
    _items: null ,
    
    _itemsRegister: null ,
    
    _nextId: 0 ,
    
    createNextId: function()
    {
        return this._nextId++;
    } ,
    
    initialize: function( $super , manager , id )
    {
        $super(manager);
        this._id = id;
        this._request = new Aitoc_Aitholiday.Admin.Request();
        this._body = Aitoc_Aitholiday.getBody();
        this._init();
    } ,
    
    _init: function()
    {
        this._request.send(['-','-','load'],this.drawResponse.bind(this),{
            parameters: {id: this._id}
        });
    } ,
    
    drawResponse: function( response )
    {
        this._draw(response.responseText);
    } ,
    
    _getView: function()
    {
        return $(this._id);
    } ,
    
    _getDragger: function()
    {
        return $(this._id+'_dragger');
    } ,
    
    _getApply: function()
    {
        return $(this._id+'_apply');
    } ,
    
    _getCancel: function()
    {
        return $(this._id+'_cancel');
    } ,
    
    _getClose: function()
    {
        return $(this._id+'_close');
    } ,
    
    _getLoading: function()
    {
        return $(this._id+'_loading');
    } ,
    
    _getItemsContainer: function()
    {
        if (!this._getView())
        {
            return null;
        }
        return $(this._getView().getElementsBySelector('.items')[0]);
    } ,
    
    _getItems: function()
    {
        if (!this._getView())
        {
            return [];
        }
        return this._getItemsContainer().getElementsBySelector('.item');
    } ,
    
    getItems: function()
    {
        if (!this._getView())
        {
            return [];
        }
        if (!this._items)
        {
            this._items = [];
            this._itemsRegister = {};
            this._getItems().each(function( item ) {
                var tmp = new Aitoc_Aitholiday.Decoration.PaletteControl(this,item);
                this._items[this._items.length] = tmp;
                this._itemsRegister[tmp.getId()] = tmp;
            }.bind(this));
        }
        return this._items;
    } ,
    
    _draw: function ( html )
    {
        if (this._drawed)
        {
            this._getView().replace(html);
        }
        else
        {
            this.clearView();
            this._body.insert(html);
            this._drawed = true;
        }
        setTimeout(this.updateView.bind(this),100);
    } ,
    
    clearView: function()
    {
        jQueryAitoc(this._getView()).draggable('destroy');
        
        this.getItems().each(function (item) {
            item.clearView();
        });
    } ,
    
    _moveToStartPosition: function()
    {
        var left = document.viewport.getWidth()-this._getView().getWidth();
        var top = document.viewport.getHeight()-this._getView().getHeight();
        this._getView().setStyle("left: "+left+"px; top: "+top+"px;");
        this.getDecorationManager().update();
    } ,
    
    updateView: function()
    {
        jQueryAitoc(this._getView()).draggable({
            handle: this._getDragger()
        });
        
        this.getItems().each(function( item )
        {
            item.updateView();
        });
        
        this._getApply().observe('click',this.onClickApply.bind(this));
        this._getCancel().observe('click',this.onClickCancel.bind(this));
        this._getClose().observe('click',this.onClickClose.bind(this));
        this._moveToStartPosition();
    } ,
    
    onClickClose: function()
    {
        this._request.send(['-','-','close'],function()
        {
            document.location.href = document.URL;
        }.bind(this));
    } ,
    
    stopLoading: function()
    {
        this._getApply().setStyle("display: block;");
        this._getCancel().setStyle("display: block;");
        this._getLoading().setStyle("display: none;");
    } ,
    
    beginLoading: function()
    {
        this._getApply().setStyle("display: none;");
        this._getCancel().setStyle("display: none;");
        this._getLoading().setStyle("display: block;");
    } ,
    
    updateDecortaion: function()
    {
        this.getDecorationManager().update(this.stopLoading.bind(this));
    } ,
    
    onClickCancel: function()
    {
        this.beginLoading();
        this.updateDecortaion();
    } ,
    
    onClickApply: function()
    {
        this.beginLoading();
        var data = {
            location: Aitoc_Aitholiday.location ,
            palette: this._id ,
            data: this.getDecorationManager().controlsToObject()
        };
        this._request.send(['-','-','apply'],this.updateDecortaion.bind(this),{
            parameters: {data: $H(data).toJSON()}
        });
    }
    
});