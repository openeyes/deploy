set :stage, :staging

server 'release-gateway.acrossopeneyes.com',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/portal_frontend'
set :branch, ENV.fetch('branch', 'master')
set :npm_flags, '--silent --no-progress --production'
set :gulp_env, 'staging'
