local base = _G

module('Factory')

function setBaseClass(class, baseClass)
  base.assert(baseClass.mtab)
  base.setmetatable(class, baseClass.mtab)
end

function create(class, ...)
  local w = {}
  setBaseClass(w, class)
  w:construct(base.unpack(arg))

  return w
end