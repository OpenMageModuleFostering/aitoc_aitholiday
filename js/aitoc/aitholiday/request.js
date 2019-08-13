/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

Aitoc_Aitholiday.Request = Class.create(
{
    
    urlTemplate: null ,
    
    defaultModule: null ,
    
    defaultController: null ,
    
    defaultMethod: null ,
    
    initialize: function( type )
    {
        if (Aitoc_Aitholiday.REQUEST_USER == type)
        {
            this.urlTemplate = Aitoc_Aitholiday.requestUrlTemplate;
            this.defaultModule = Aitoc_Aitholiday.defaultModule;
            this.defaultController = Aitoc_Aitholiday.defaultController;
            this.defaultMethod = Aitoc_Aitholiday.defaultMethod;
        }
        else
        {
            this.urlTemplate = Aitoc_Aitholiday.requestAdminUrlTemplate;
            this.defaultModule = Aitoc_Aitholiday.defaultAdminModule;
            this.defaultController = Aitoc_Aitholiday.defaultAdminController;
            this.defaultMethod = Aitoc_Aitholiday.defaultAdminMethod;
        }
    } ,
    
    hasHttpsProtocol: function( url )
    {
        return Aitoc_Aitholiday.hasHttpsProtocol(url);
    } ,
    
    _prepareUrl: function( request )
    {
        if ('string' == typeof request)
        {
            var url = request;
        }
        else
        {
            request = request || [];
            var url = this.urlTemplate;
            if (request.length < 1 || request[0] == '-')
            {
                request[0] = this.defaultModule;
            }
            if (request.length < 2 || request[1] == '-')
            {
                request[1] = this.defaultController;
            }
            if (request.length < 3 || request[2] == '-')
            {
                request[2] = this.defaultMethod;
            }
            url = url.replace('%module%',request[0]);
            url = url.replace('%controller%',request[1]);
            url = url.replace('%method%',request[2]);
        }
        return url;
    } ,
    
    send: function ( request , callback , options )
    {
        options = options || {};
        if (!options.method)
        {
            options.method = 'post';
        }
        options.onSuccess = callback || this.realizeResponse.bind(this);
        var url = this._prepareUrl(request);
        if (this.hasHttpsProtocol(url) == this.hasHttpsProtocol(document.URL))
        {
            new Ajax.Request(url,options);
        }
        else if (options.onProtocolFailure)
        {
            options.onProtocolFailure();
        }
    } ,
    
    realizeResponse: function ( response )
    {
    }
    
});

Aitoc_Aitholiday.Admin.Request = Class.create(Aitoc_Aitholiday.Request,
{
    
    initialize: function ( $super )
    {
        $super(Aitoc_Aitholiday.REQUEST_ADMIN);
    } ,
    
    realizeResponse: function ( $super , response )
    {
        if (1 == response.responseJSON.status)
        {
            if (response.responseJSON && Aitoc_Aitholiday.config.palette)
            {
                var manager = new Aitoc_Aitholiday.Palette.Manager();
                manager.createControl(response.responseJSON.paletteId);
            }
            else
            {
                Aitoc_Aitholiday.initPublic();
            }
        }
        $super(response);
    } ,
    
    send: function ( $super , request , callback , options )
    {
        options = options || {};
        options.onProtocolFailure = function()
        {
            var manager = new Aitoc_Aitholiday.Decoration.Manager();
            manager.showDecoration();
            var request = new Aitoc_Aitholiday.User.Request(manager);
            request.send();
        };
        $super(request,callback,options);
    }
    
});

Aitoc_Aitholiday.User.Request = Class.create(Aitoc_Aitholiday.Request,
{
    
    _manager: null ,
    
    _defaultData: null ,
    
    setManager: function( manager )
    {
        this._manager = manager;
    } ,
    
    send: function ( $super , request , callback , options )
    {
        options = options || {};
        options.parameters = options.parameters || {};
        options.parameters = $H(this._defaultData).merge(options.parameters).toObject();
        $super(request,callback,options);
    } ,
    
    initialize: function ( $super , manager )
    {
        $super(Aitoc_Aitholiday.REQUEST_USER);
        this.setManager(manager);
        this._defaultData = {
            location: $H(Aitoc_Aitholiday.location).toJSON()
        };
    } ,

    realizeResponse: function ( $super , response )
    {
        if (this._manager)
        {
            this._manager.realizeResponse(response);
        }
        $super(response);
    }
    
});