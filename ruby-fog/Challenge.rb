require 'inifile'
require 'fog'

module Challenge
  
  def getIniValues() 
    myini = IniFile.load(Dir.home + '/.rackspace_cloud_credentials')
    return myini
  end
  
  def getUsername()
    myini = getIniValues() 
    return myini['credentials']['username']
  end
  
  def getApiKey()
    myini = getIniValues() 
    return myini['credentials']['api_key']
  end
  
  def getFog() 
    return Fog::Compute.new(
      :provider => 'Rackspace', 
      :rackspace_username => getUsername(), 
      :rackspace_api_key => getApiKey(),
      :openstack_region => 'IAD'
      )  
  end

end