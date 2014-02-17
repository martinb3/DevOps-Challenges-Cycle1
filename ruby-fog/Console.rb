#!/usr/bin/env ruby

# Console
#

require "./Challenge6.rb"
argsneeded = "Needs arguments challenge6 or challenge8"

if ARGV.length <= 0
  abort(argsneeded)
end

command = ARGV.first()

case command
  when "challenge6" 
    Challenge6.new().challenge()
else
  abort(argsneeded)
end