set :stage, :vagrant

server 'localhost',
  user: 'vagrant',
  roles: %w{web},
  ssh_options: {
    port: 2222,
    keys: ['~/.vagrant.d/insecure_private_key']
  }

set :deploy_to, '/var/www/openeyes'
set :branch, 'develop'
#set :composer_install_flags, '--no-interaction --quiet'
