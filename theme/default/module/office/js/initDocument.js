/* initDocument.js
 *
 * Created:
 *   7/5/2021, 12:31:07 PM
 * Last edited:
 *   7/5/2021, 12:31:08 PM
 * Auto updated?
 *   NO
 *
 * Description:
 *   This file will contains all the configuration and script need for
 *   loading DOCX,XLSX,PPTX documents type 
 **/

(function (global, $) {
  // Those are default configurations 
  // if we want to have more configuration we can do directly by setting them in HTML files, even if not exist here
  // example: DocsServer.docsPermissions.help=true  activate help section
  var DocsServer = {
    // Configure Permsions
    // see the onlyoffice  documentation https://api.onlyoffice.com/editors/config/document/permissions
    docsPermissions: {
      comment: false,
      copy: false,
      download: false,
      edit: false,
      review: false,
      comments: false,
      print: false
    },
    docsEditorConfig: {
      lang: "en", //default
      mode: "view", // always view
      // Configure Customization Here
      // see the onlyoffice  documentation https://api.onlyoffice.com/editors/config/editor/customization
      customization: {
        chat: false,
        help: false,
        hideRightMenu: true,
        plugins: false,
        toolbarHideFileName: false,
        compactToolbar: true,
        plugins: false,
        reviewDisplay: "original",
        trackChanges: false,
        uiTheme: "theme-dark",
        zoom: -2,
        logo: {
          image: "",
          imageEmbedded: "",
          url: ""
        }
      },
      events: {
        onError: function (e) {
          console.log("DocsAPI==>onError Event");
        },
        onDocumentReady: function (e) {
          console.log("DocsAPI==>onDocumentReady Event");
        }
      }
    },
    docsURL: "", // default empty string 
    title: "",
    docsType: "embedded", //default embeded 
    docsExt: "docx",
    docsPlaceHolder: "placeholder",
    height: "100%",
    width: "100%",
    date: new Date()
  };

  DocsServer.DocsEditor = {
    LoadDocuments: function () {
      if (!DocsServer.docsURL || DocsServer.docsURL === "" || DocsServer.docsURL === undefined)
        throw 'URL can\'t be empty!';

      global.docEditor = new DocsAPI.DocEditor(DocsServer.docsPlaceHolder, {
        type: DocsServer.docsType,
        width: DocsServer.height,
        height: DocsServer.width,
        documentType: DocsServer.Helper.getDocumentType(DocsServer.docsExt),
        document: {
          title: DocsServer.docsTitle,
          url: DocsServer.docsURL,
          fileType: DocsServer.docsExt,
          key: DocsServer.Helper.getDocumentKey(new Date().toString()),
          permissions: DocsServer.docsPermissions
        },
        editorConfig: DocsServer.docsEditorConfig,
        events: DocsServer.docsEditorConfig.events
      });
    }
  };

  DocsServer.Helper = {
    /**
     * Function that will return document type base of file type 
     * @param {*} ext document extension exp: .xlsx || xls , doc || docx etc.
     * @returns  return doc|| docx ==> word
     */
    getDocumentType: function (ext) {
      if (".doc.docx.docm".indexOf(ext) != -1) return "word";
      if (".xls.xlsx.xlt.csv".indexOf(ext) != -1) return "cell";
      if (".pps.ppsx.ppt.pptx".indexOf(ext) != -1) return "slide";
      return null;
    },

    /**
     * --- Not requeired
     * Function that will genarete a random key,
     * @param {String} k , contains string that will converted to a uniq key
     * @returns return auniq key 
     */
    getDocumentKey: function (k) {
      var result = k.replace(new RegExp("[^0-9-.a-zA-Z_=]", "g"), "_") + (new Date()).getTime();
      return result.substring(result.length - Math.min(result.length, 20));
    }
  };

  window.DocsServer = DocsServer;
})(window, $);