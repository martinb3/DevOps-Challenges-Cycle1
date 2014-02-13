package org.mbs3.rax.devops.cycle1

object Console {
  def main(args: Array[String]): Unit = {

    if (args.isEmpty) {
      println("""Usage: console [<options>]""")
      return
    }

    val arglist = args.toList
    val firstOption = arglist.head
    
    firstOption match {
      case "challenge5" => Challenge5.doChallenge(arglist.tail)
      case "challenge6" => Unit
      case "challenge7" => Unit
      case "challenge8" => Unit
      case _ => println("Option not found")
    }
  }

}