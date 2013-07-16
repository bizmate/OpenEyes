var connect = require('connect');
var path = require('path');
var http = require('http');

module.exports = function(grunt) {

  /* Spin up a connect server to display the documentation. */
  grunt.registerMultiTask('viewdocs', function() {   

    // A little trick to keep this task running indefinitely 
    this.async();

    // Merge the options with default values
    var options = this.options({
      port: 9001,
      base: '.',
      hostname: 'localhost'
    });

    startServer();

    // Starts a connect server to display the docs (using static and directory middleware).
    function startServer() {
    
      var app = connect();
      app.use(connect.static(options.base));
      app.use(connect.directory(options.base));

      var server = http.createServer(app);
      server.listen(options.port, options.hostname);

      server.on('listening', function() {
        grunt.log.writeln('Started docs server on ' + (options.host || 'localhost') + ':' + options.port);
        openInChrome();
      });

      server.on('error', function(err) {
        grunt.fatal(err);
      });
    }

    // Spawn a new child process to open up the docs in a new tab in chrome.
    function openInChrome() {

      var cp = require('child_process').spawn('open', [
        '-a',
        '/Applications/Google Chrome.app',
        'http://' + options.hostname + ':' + options.port
      ]);

      // Silently ignore errors
      cp.on('error', function(){});
    }        
  });
};