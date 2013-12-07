# ==================
# @ Start
# ==================

def muteInitiate load
  @muteMemory = Hash.new  
  @muteMemory['mute'] = Hash.new
  @muteMemory['mute']['data'] = load
  @muteMemory[load]
end

def muteParseScript mute_script

  @muteReturn = ""
  lines = Hash.new
  lines = mute_script.lines
  lines.each do |line|
    muteParseLines(line)
  end
  return @muteReturn
end

# ==================
# @ Line
# ==================

def muteParseLines mute_line

  # memoryname
  memoryname = mute_line.scan(/[a-z0-9#]+/i)[0]

  # Create memory
  if !@muteMemory[memoryname]
    @muteMemory[memoryname] = Hash.new
    @muteMemory[memoryname]['cond'] = Hash.new
    @muteMemory[memoryname]['data'] = Hash.new
    @muteMemory[memoryname]['oper'] = Hash.new
  end

  # cond
  cond = mute_line.scan(/\((.*?)\)/)
  if cond[0] then @muteMemory[memoryname]['cond'] = cond end
  
  # data
  data = mute_line.scan(/\[(.*?)\]/)
  if data[0] then @muteMemory[memoryname]['data'] = muteParsedata(data) end

  # oper
  oper = mute_line.scan(/\{(.*?)\}/)
  if oper[0] then @muteMemory[memoryname]['oper'] = oper end

  # Run!
  if muteParsecond(@muteMemory[memoryname]['cond']) > 0 then
    muteOperate(@muteMemory[memoryname]['oper'])
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
    @muteReturn += opertringFirst
  elsif opertringSplitted[0].include? "@"
    @muteReturn += opertringFirst.sub('@',muteMemoryAccessor(opertringSplitted[1]))
  else
    @muteReturn += opertringSplitted.to_s
  end

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
  if @muteMemory[memoryAccessor]
    # 1 memoryAccessorIndex:int
    if memoryAccessorIndex && @muteMemory[memoryAccessor]['data'][memoryAccessorIndex.to_i]
      return @muteMemory[memoryAccessor]['data'][memoryAccessorIndex.to_i].to_s
    # 1 memoryAccessorIndex:string
    elsif memoryAccessorIndex && @muteMemory[memoryAccessor]['data'][memoryAccessorIndex]
      return @muteMemory[memoryAccessor]['data'][memoryAccessorIndex].to_s
    # 0 memoryAccessorIndex
    elsif @muteMemory[memoryAccessor]['data'][0] && @muteMemory[memoryAccessor]['data'].length == 1
      return @muteMemory[memoryAccessor]['data'][0].to_s
    # 0 memoryAccessorCount
    elsif @muteMemory[memoryAccessor]['data'][0] && @muteMemory[memoryAccessor]['data'].length > 1
      return @muteMemory[memoryAccessor]['data'].length.to_s
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

# Test String

input_string = '
Begin
{mute}
a[5]{"@",a}
a[9]{"test"}
{/mute}
Middle
{mute}
a{"@",a}
{/mute}
Some stuff'

# Prestore data in @muteMemory
prefill = {"title" => "indexer", "tester" => "default"}
muteInitiate(prefill)

# Customize delimiter
testScript = input_string.scan(/(?:\{mute\})([\w\W]*?)(?=\{\/mute\})/)

testScript.each do |k,v|
  input_string = input_string.sub(k,muteParseScript(k).to_s)

end
input_string = input_string.gsub("{mute}","").gsub("{/mute}","")

puts input_string



