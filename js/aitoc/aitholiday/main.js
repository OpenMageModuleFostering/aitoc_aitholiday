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
    
    init: function() 
    {
        var request = new Aitoc_Aitholiday.Admin.Request();
        request.send([],null,{
            parameters: {store_id: this.storeViewId}
        });
    }
    
};