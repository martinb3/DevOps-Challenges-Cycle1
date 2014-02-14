package org.mbs3.rax.devops.cycle1

object Console extends App{
	if (args.isEmpty) {
	  println("""Usage: console [<options>]""")
	  sys.exit(0)
	}
	
	val arglist = args.toList
	val firstOption = arglist.head
	
	firstOption match {
	  case "challenge5" => Challenge5.doChallenge(arglist.tail)
	  case "challenge6" => Challenge6.doChallenge(arglist.tail)
	  case "challenge7" => Challenge7.doChallenge(arglist.tail)
	  case "challenge8" => Unit
	  case _ => println("Option '"+firstOption+"' not found")
	}
}