set :stage, :production

server 'oegateway.org.uk',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/portal_frontend'
set :branch, 'master'
set :npm_flags, '--silent --no-progress --production'
set :gulp_env, 'production'
