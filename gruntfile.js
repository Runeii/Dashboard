module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    watch: {
      php: {
        files: ['*.php', 'includes/**/*.php'],
        tasks: ['notify:php']
      },
      sass: {
        files: 'assets/sass/screen.scss',
        tasks: ['sass:dev', 'notify:sass'],
      },
      js: {
        files: [
          'assets/js/src/*.js'
        ],
        tasks: ['babel', 'concat','notify:js']
      }
    },
    php: {
      default : {
        options: {
				  hostname: 'localhost',
        	port: 8010,
  				keepalive: false,
  				open: false
        }
      }
    },
    browserSync: {
      files: {
        src : [
          'assets/css/style-min.css',
          'assets/js/scripts-min.js',
          '*.php',
          'includes/**/*.php'
        ],
      },
      options: {
        watchTask: true,
        notify: false,
        open: true,
        port: '8080',
				proxy: '<%= php.default.options.hostname %>:<%= php.default.options.port %>'
      }
    },
    sass: {
      options: {
          sourceMap: true
      },
      dev: {
        files: {
          'assets/css/style-min.css': 'assets/sass/screen.scss'
        }
      },
      dist: {
        files: {
          'assets/css/style.css': 'assets/sass/screen.scss'
        }
      }
    },
    postcss: {
      options: {
        map: false,
        processors: [
          require('autoprefixer')({browsers: ['last 1 version', '> 1%', 'ie 8']})
        ]
      },
      dist: {
        files: {
          'assets/css/style-prefixed.css': ['assets/css/style.css']
        }
      }
    },
    cssmin: {
      combine: {
        files: {
          'assets/css/style-min.css': ['assets/css/style-prefixed.css'/*, 'assets/css/vendors/*' */]
        },
      },
    },
    babel: {
        options: {
            presets: ['env'],
            sourceMap: true
        },
        dist: {
            files: [{
                expand: true,
                cwd: 'assets/js/src/',
                src: ['*.js', '!sources.js'],
                dest: 'assets/js/babel/'
            }]
        }
    },
    concat: {
      options: {
        separator: ';',
        stripBanners: true
      },
      stageone: {
        src: ['assets/js/src/sources.js','assets/js/babel/*.js'],
        dest: 'assets/js/scripts-min.js',
      }
    },
    notify: {
      options: {
        enabled: true,
        max_jshint_notifications: 5, // maximum number of notifications from jshint output
        title: "Comms Dashboard", // defaults to the name in package.json, or will use project directory's name
        success: true, // whether successful grunt executions should be notified automatically
        duration: 3 // the duration of notification in seconds, for `notify-send only
      },
      sass: {
        options: {
          title: 'SASS Compiled',  // optional
          message: 'Pushing updated files to browser', //required
        }
      },
      php: {
        options: {
          title: 'PHP changed',  // optional
          message: 'Pushing updated files to browser', //required
        }
      },
      js: {
        options: {
          title: 'JS Compiled',  // optional
          message: 'Pushing updated files to browser', //required
        }
      }
    }
  });

  // Server
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-php');
  grunt.loadNpmTasks('grunt-browser-sync');
  grunt.loadNpmTasks('grunt-notify');
  //CSS
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  //JS
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-babel');
  // Register the default tasks.

  grunt.registerTask('default', ['php', 'browserSync', 'watch']);
  grunt.registerTask('prod', ['sass:dist', 'postcss', 'cssmin','babel','concat']);
};
