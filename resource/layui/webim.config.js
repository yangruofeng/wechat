/**
 * git do not control webim.config.js
 * everyone should copy webim.config.js.demo to webim.config.js
 * and have their own configs.
 * In this way , others won't be influenced by this config while git pull.
 *
 */
var WebIM = {};
WebIM.config = {
    /*
     * XMPP server
     */
	xmppURL: 'im-api.easemob.com',
    /*
     * Backend REST API URL
     */
    apiURL: (location.protocol === 'https:' ? 'https:' : 'http:') + '//a1.easemob.com',
    /*
     * Application AppKey
     */
    //appkey: WEBIM_APPKEY,
    /*
     * Whether to use HTTPS
     * @parameter {Boolean} true or false
     */
    https: location.protocol === 'https:' ? 'https:' : 'http:',
    
    
	isMultiLoginSessions: false,
    isAutoLogin: true
};
WebIM.config.appkey = WEBIM_APPKEY;
