require('../css/app.scss');

var $ = require('jquery');
window.$ = $;
window.jQuery = $;

require('bootstrap-sass');

if($('#flashModal').length) $('#flashModal').modal();