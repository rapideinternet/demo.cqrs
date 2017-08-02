
# CQRS, Event Sourcing and Modular Laravel

## Installation

### Homestead
For this project we use homestead to setup our development environment. It has been tested against Homestead 3.0.0


### Project

Run composer install (broken till package is updated)
    
    composer install
    


### Elasticsearch
To install elasticsearch on Homestead complete the following steps


Promote to root
   
    sudo -s
   
Install java

    apt update
    apt-get install default-jre

Check java version
    
    java -version 
    

Import the apt key

    wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -

Update sources

    apt-get update
    
Get the deb

    wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.3.0.deb
    
Install the deb file
    
    sudo dpkg -i elasticsearch-5.3.0.deb
    
Add elasticsearch to boot

    sudo update-rc.d elasticsearch defaults 95 10
    
Start elastic search 

    /etc/init.d/elasticsearch start
    
Check if it works

    curl -XGET 'http://localhost:9200'
    
Bonus Kibana

    wget https://artifacts.elastic.co/downloads/kibana/kibana-5.0.2-amd64.deb
    dpkg -i kibana-5.0.2-amd64.deb
    sudo update-rc.d kibana defaults 95 10

Change

    nano /etc/kibana/kibana.yml
    
Edit

    server.host: 192.168.10.10

Start

    /etc/init.d/kibana start



    
