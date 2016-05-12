set :stage, :develop

server '52.49.56.3',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/openeyes'
set :branch,  ENV.fetch('branch', 'develop')
