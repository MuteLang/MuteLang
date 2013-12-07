# ==================
# @ Start
# ==================

def muteInitiate load
  @memory = Hash.new  
  @memory['mute'] = Hash.new
  @memory['mute']['data'] = load
  @memory[load]
end

def muteParseScript mute_script
  lines = Hash.new
  lines = mute_script.lines
  lines.each do |line|
    muteParseLines(line)
  end
end

# ==================
# @ Line
# ==================

def muteParseLines mute_line

  # memoryname
  memoryname = mute_line.scan(/[a-z0-9#]+/i)[0]

  # Create memory
  if !@memory[memoryname]
    @memory[memoryname] = Hash.new
    @memory[memoryname]['cond'] = Hash.new
    @memory[memoryname]['data'] = Hash.new
    @memory[memoryname]['oper'] = Hash.new
  end

  # cond
  cond = mute_line.scan(/\((.*?)\)/)
  if cond[0] then @memory[memoryname]['cond'] = cond end
  
  # data
  data = mute_line.scan(/\[(.*?)\]/)
  if data[0] then @memory[memoryname]['data'] = muteParsedata(data) end

  # oper
  oper = mute_line.scan(/\{(.*?)\}/)
  if oper[0] then @memory[memoryname]['oper'] = oper end

  # Run!
  if muteParsecond(@memory[memoryname]['cond']) > 0 then
    muteOperate(@memory[memoryname]['oper'])
  end

end

# ==================
# @ Dynamos: cond
# ==================

def muteParsecond cond

  cond.each do |condition,details|

    oper = condition.gsub(/[0-9a-z.]/i, '')
    conditionPiece = condition.split(/[^0-9a-z.]/i)

    # Condition is just if exist
    if oper.to_s == ""
      if muteMemoryAccessor(conditionPiece[0]) == conditionPiece[0]
        return 0
      else
        return 1
      end
    end

    # Pieces
    val1 = muteMemoryAccessor(conditionPiece[0])
    val2 = muteMemoryAccessor(conditionPiece[1])

    # Break on false case
    if muteParseoperolver(val1,oper,val2) == 0 
      return 0
    end

  end

  return 1
  
end

def muteParseoperolver val1,oper,val2

  case oper
    when ">" then return muteOperGreater(val1,val2)
    when "<" then return muteOperSmaller(val1,val2)
    when "=" then return muteOperEqual(val1,val2)
    when "+" then return muteOperAdd(val1,val2)
    when "-" then return muteOperSub(val1,val2)
  end
  return 0

end

# ==================
# @ Dynamos: data
# ==================

def muteParsedata data
  
  contentSplitted = Hash.new
  contentSplittedWithIndexes = Hash.new
  data.each do |content,details|
    # Split every block
    contentSplitted =  content.split(",")
    count = 0
    contentSplitted.each do |rawValue|
      # Index found
      if rawValue.include? ":"
        indexSplitted = rawValue.split(":")
        contentSplittedWithIndexes[indexSplitted[0]] = indexSplitted[1]
      # Index not found
      else
        contentSplittedWithIndexes[count] = muteParsedataOperate(rawValue)
        count += 1
      end
    end
  end  
  return contentSplittedWithIndexes

end

def muteParsedataOperate rawValue

  # Remove quotes from strings
  rawValue = rawValue.gsub('"','')

  # Find an operation
  oper = rawValue.gsub(/[0-9a-z. ]/i, '')

  # If there are no oper
  if oper.to_s == "" then return rawValue end

  # Perform operation
  dataPiece = rawValue.split(/[^0-9a-z.]/i)
  val1 = muteMemoryAccessor(dataPiece[0])
  val2 = muteMemoryAccessor(dataPiece[1])
  return muteParseoperolver(val1,oper,val2)

end

# ==================
# @ Dynamos: oper
# ==================

def muteOperate operation

  operation.each do |operation,v|
    # Print
    if operation.scan(/\"(.*?)\"/).length > 0 
      muteOperatePrint(operation)
    # Fire
    else
      muteParseLines(operation)
    end
  end

end

def muteOperatePrint opertring

  # Look for 
  opertringSplitted =  opertring.split(",")
  opertringFirst = opertringSplitted[0].scan(/\"(.*?)\"/)[0].to_s

  # Simple string
  if opertringSplitted.length == 1
    puts opertringFirst
  elsif opertringSplitted[0].include? "@"
    puts opertringFirst.sub('@',muteMemoryAccessor(opertringSplitted[1]))
  else
    puts opertringSplitted.to_s
  end

end

# ==================
# @ Consolelog
# ==================

def muteMemoryPrint
  p @memory
end

# ==================
# @ Tools
# ==================

def muteMemoryAccessor memoryAccessor

  if memoryAccessor.include? "."
    memoryAccessorIndex = muteMemoryAccessor(memoryAccessor.split(".")[1].to_s)
    memoryAccessor = memoryAccessor.split(".")[0].to_s
  end

  # If variable name is used
  if @memory[memoryAccessor]
    # 1 memoryAccessorIndex:int
    if memoryAccessorIndex && @memory[memoryAccessor]['data'][memoryAccessorIndex.to_i]
      return @memory[memoryAccessor]['data'][memoryAccessorIndex.to_i].to_s
    # 1 memoryAccessorIndex:string
    elsif memoryAccessorIndex && @memory[memoryAccessor]['data'][memoryAccessorIndex]
      return @memory[memoryAccessor]['data'][memoryAccessorIndex].to_s
    # 0 memoryAccessorIndex
    elsif @memory[memoryAccessor]['data'][0] && @memory[memoryAccessor]['data'].length == 1
      return @memory[memoryAccessor]['data'][0].to_s
    # 0 memoryAccessorCount
    elsif @memory[memoryAccessor]['data'][0] && @memory[memoryAccessor]['data'].length > 1
      return @memory[memoryAccessor]['data'].length.to_s
    end
  end
  return memoryAccessor

end

def mutePureType unknownType

  intType = unknownType.gsub(/[^0-9]/i,'').to_i.to_s

  if unknownType == intType
    return "int"
  else 
    return "string"
  end

end

def muteOperGreater val1, val2
  if val1 > val2 then return 1 end
  return 0
end

def muteOperSmaller val1, val2
  if val1 < val2 then return 1 end
  return 0
end

def muteOperEqual val1, val2
  if val1 == val2 then return 1 end
  return 0
end

def muteOperAdd val1, val2
  if mutePureType(val1) == "int" && mutePureType(val2) == "int"
    return val1.to_i+val2.to_i
  else
    return val1.to_s+val2.to_s
  end
end

def muteOperSub val1, val2
  if mutePureType(val1) == "int" && mutePureType(val2) == "int"
    return val1.to_i-val2.to_i
  else
    return val1.to_s.sub(val2.to_s,"")
  end
end

prefill = {"title" => "indexer", "tester" => "default"}

muteInitiate(prefill)

input_string = '
<p>Some stuff</p>
:::
a[5]
:::
<p>Some stuff</p>'

muteStart = ":::"
muteEnd   = ":::"
testScript = input_string[/#{muteStart}(.*?)#{muteEnd}/m, 1]

muteParseScript(testScript)

testString2 = '
test{"> @ ",a}
test2{"+ @",mute.title}
'
muteParseScript(testString2)
