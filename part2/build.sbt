name := "DevOps-Challenges-Cycle1"

libraryDependencies += "org.apache.jclouds" % "jclouds-all" % "1.7.1"

libraryDependencies += "jline" % "jline" % "2.11"

libraryDependencies += "com.google.code.findbugs" % "jsr305" % "2.0.3" % "provided"

mainClass in (Compile, run) := Some("org.mbs3.rax.devops.cycle1.Console")

