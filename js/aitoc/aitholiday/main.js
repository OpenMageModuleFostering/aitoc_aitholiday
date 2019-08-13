/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

Aitoc_Aitholiday =
{
        
    requestAdminUrlTemplate: null ,
    
    defaultAdminModule: null ,
    
    defaultAdminController: null ,
    
    defaultAdminMethod: null ,
    
    requestUrlTemplate: null ,
    
    defaultModule: null ,
    
    defaultController: null ,
    
    defaultMethod: null ,
    
    storeViewId: null ,
    
    baseUrl: null ,
    
    location: null ,
    
    language: null ,
    
    REQUEST_ADMIN: 'admin' ,
    
    REQUEST_USER: 'user' ,
    
    Request: null ,
    
    _body: null ,
    
    getBody: function()
    {
        if (!this._body)
        {
            this._body = $(document.getElementsByTagName('body')[0]);
        }
        return this._body;
    } ,
    
    Admin: {
    
        Request: null
        
    } ,
    
    User: {
        
        Request: null 
        
    } ,
    
    Decoration: {
        
        Manager: null ,
        
        Abstract: null ,
        
        Control: null ,
        
        PaletteControl: null ,
        
        AdminControl: null
        
    } ,
    
    Palette: 
    {
        
        Abstract: null ,
        
        Manager: null ,
        
        Control: null
        
    } ,
    
    hasHttpsProtocol: function( url )
    {
        return 'https://' == url.match(/^https:\/\//);
    } ,
    
    init: function() 
    {
        if (this.hasHttpsProtocol(document.URL))
        {
            this.baseUrl = this.baseUrl.replace('http://','https://');
            this.requestAdminUrlTemplate = this.requestAdminUrlTemplate.replace('http://','https://');
            this.requestUrlTemplate = this.requestUrlTemplate.replace('http://','https://');
        }
        if (Aitoc_Aitholiday.config.palette)
        {
            var request = new Aitoc_Aitholiday.Admin.Request();
            request.send([],null,{
                parameters: {store_id: this.storeViewId}
            });
        }
        else if (Aitoc_Aitholiday.config.decoration)
        {
            this.initPublic();
        }
    } ,
    
    initPublic: function()
    {
        var manager = new Aitoc_Aitholiday.Decoration.Manager();
        manager.showDecoration();
        var request = new Aitoc_Aitholiday.User.Request(manager);
        request.send();
    }
    
};