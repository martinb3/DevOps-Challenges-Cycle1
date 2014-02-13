package org.mbs3.rax.devops.cycle1

import jline.console.ConsoleReader

abstract trait Challenge {
	def doChallenge(args: List[String]): Unit

   final val PROVIDER = scala.util.Properties.propOrElse("provider.cdb", "rackspace-clouddatabases-us");
   final val ZONE = scala.util.Properties.propOrElse("zone", "IAD");
	
	protected def getUsername() = getConfigKeyOrException("username")
	protected def getAPIKey() = getConfigKeyOrException("api_key")
	
	private def getConfigKeyOrException(key: String) : String = {
	  getConfigKeyOrNone(key) match {
	    case Some(value) => value
	    case None => throw new IllegalStateException("No key '"+key+"' in config file")
	  }
	}
	
	private def getConfigKeyOrNone(key: String) : Option[String] = {
	  val lines = getConfigLines

	  val matching = lines.filter(s => {s.contains("=") && s.trim().startsWith(key)})
	  
	  if(matching.isEmpty) return None
	  
	  val parts = matching.next.split("=")
	  if(parts.length < 2)
	    return None
	    
	  return Some(parts.tail.head)
	}

	private def getRAXRC() = {
	  val p = scala.util.Properties;
	  val home = p.envOrElse("HOME", "/home")
	  p.propOrElse("RAXRC", home+"/"+".rackspace_cloud_credentials")
	}
	
	private def getConfigLines() = scala.io.Source.fromFile(getRAXRC()).getLines
	
	
	val reader = new ConsoleReader()
	protected def prompt(prompt: String) = reader.readLine(prompt)
	protected def prompt() = reader.readLine()

}