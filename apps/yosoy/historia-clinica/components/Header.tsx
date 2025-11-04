
import React from 'react';
import { Stethoscope } from 'lucide-react';

interface HeaderProps {
  setCurrentPage: (page: 'dashboard' | 'patientList') => void;
}

const Header: React.FC<HeaderProps> = ({ setCurrentPage }) => {
  return (
    <header className="bg-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <div className="flex items-center">
            <button onClick={() => setCurrentPage('dashboard')} className="flex items-center space-x-3 text-purple-600">
              <div className="flex items-center space-x-2">
                <div className="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center">
                  <span className="text-white font-bold text-sm">YS</span>
                </div>
                <div className="flex flex-col">
                  <span className="font-bold text-xl text-purple-800">YoSoy</span>
                  <span className="text-xs text-purple-600">Historia Clínica</span>
                </div>
              </div>
            </button>
          </div>
          <nav className="hidden md:flex md:space-x-8">
            <button
              onClick={() => setCurrentPage('dashboard')}
              className="font-medium text-purple-600 hover:text-purple-800 transition-colors"
            >
              Dashboard
            </button>
            <button
              onClick={() => setCurrentPage('patientList')}
              className="font-medium text-purple-600 hover:text-purple-800 transition-colors"
            >
              Pacientes
            </button>
          </nav>
        </div>
      </div>
    </header>
  );
};

export default Header;
