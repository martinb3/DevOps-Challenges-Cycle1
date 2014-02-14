name := "DevOps-Challenges-Cycle1"

libraryDependencies += "org.apache.jclouds" % "jclouds-core" % "1.7.1"

libraryDependencies += "org.apache.jclouds.api" % "openstack-trove" % "1.7.1"

libraryDependencies += "org.apache.jclouds.api" % "openstack-nova" % "1.7.1"

libraryDependencies += "org.apache.jclouds.driver" % "jclouds-sshj" % "1.7.1"

libraryDependencies += "org.apache.jclouds.provider" % "rackspace-clouddatabases-us" % "1.7.1"

libraryDependencies += "org.apache.jclouds.provider" % "rackspace-cloudservers-us" % "1.7.1"

libraryDependencies += "org.apache.jclouds.provider" % "rackspace-cloudloadbalancers-us" % "1.7.1"

libraryDependencies += "jline" % "jline" % "2.11"

libraryDependencies += "com.google.code.findbugs" % "jsr305" % "2.0.3" % "provided"

libraryDependencies += "org.slf4j" % "slf4j-api" % "1.6.1"

libraryDependencies += "org.slf4j" % "slf4j-nop" % "1.6.1"

mainClass in (Compile, run) := Some("org.mbs3.rax.devops.cycle1.Console")

